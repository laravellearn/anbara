<?php

namespace App\Http\Controllers\Warehouse;

use App\Models\ReorderRule;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class ReorderController extends BaseController
{
    // ─── لیست قوانین ─────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        Gate::authorize('access', 'reorder.view');
        [$tenantId, $companyId] = $this->tenantCtx();

        $query = ReorderRule::forTenant($tenantId, $companyId)
            ->with(['product','warehouse','preferredSupplier'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('warehouse_id')) $query->where('warehouse_id', $request->warehouse_id);
        if ($request->boolean('active_only')) $query->active();

        $rules      = $query->paginate(20)->withQueryString();
        $warehouses = Warehouse::where('tenant_id', $tenantId)->where('is_active', true)->get();

        // ─── پیشنهادهای سفارش (کالاهایی که موجودی <= نقطه سفارش) ─────────
        $suggestions = $this->buildSuggestions($tenantId, $companyId);

        return view('warehouse.reorder.index', compact('rules','warehouses','suggestions'));
    }

    // ─── ذخیره / ویرایش قانون ────────────────────────────────────────────────
    public function store(Request $request)
    {
        Gate::authorize('access', 'reorder.manage');
        [$tenantId, $companyId] = $this->tenantCtx();

        $request->validate([
            'product_id'       => 'required|exists:products,id',
            'reorder_point'    => 'required|numeric|min:0',
            'reorder_quantity' => 'required|numeric|min:0.0001',
            'lead_time_days'   => 'required|integer|min:0',
        ]);

        ReorderRule::updateOrCreate(
            [
                'tenant_id'    => $tenantId,
                'company_id'   => $companyId,
                'product_id'   => $request->product_id,
                'warehouse_id' => $request->warehouse_id ?: null,
            ],
            [
                'preferred_supplier_id' => $request->preferred_supplier_id ?: null,
                'reorder_point'         => $request->reorder_point,
                'reorder_quantity'      => $request->reorder_quantity,
                'safety_stock'          => $request->safety_stock ?? 0,
                'lead_time_days'        => $request->lead_time_days,
                'is_active'             => true,
            ]
        );

        return back()->with('success', 'قانون سفارش ذخیره شد.');
    }

    // ─── ویرایش قانون ─────────────────────────────────────────────────────────
    public function update(Request $request, ReorderRule $reorderRule)
    {
        Gate::authorize('access', 'reorder.manage');
        abort_if($reorderRule->tenant_id !== $this->manager->getTenantId(), 403);

        $request->validate([
            'reorder_point'    => 'required|numeric|min:0',
            'reorder_quantity' => 'required|numeric|min:0.0001',
            'lead_time_days'   => 'required|integer|min:0',
        ]);

        $reorderRule->update($request->only([
            'preferred_supplier_id','reorder_point','reorder_quantity',
            'safety_stock','lead_time_days','is_active',
        ]));

        return back()->with('success', 'قانون به‌روز شد.');
    }

    // ─── حذف قانون ────────────────────────────────────────────────────────────
    public function destroy(ReorderRule $reorderRule)
    {
        Gate::authorize('access', 'reorder.manage');
        abort_if($reorderRule->tenant_id !== $this->manager->getTenantId(), 403);
        $reorderRule->delete();
        return back()->with('success', 'قانون حذف شد.');
    }

    // ─── دریافت داده برای فرم modal ──────────────────────────────────────────
    public function formData()
    {
        [$tenantId, $companyId] = $this->tenantCtx();
        return response()->json([
            'products'   => Product::where('tenant_id', $tenantId)->where('is_active', true)->orderBy('title')->get(['id','title','sku','minimum_stock']),
            'warehouses' => Warehouse::where('tenant_id', $tenantId)->where('is_active', true)->get(['id','title']),
            'suppliers'  => Contact::where('tenant_id', $tenantId)->where('type', 'supplier')->get(['id','name']),
        ]);
    }

    // ─── ساخت پیشنهادهای سفارش ────────────────────────────────────────────────
    private function buildSuggestions(int $tenantId, int $companyId): \Illuminate\Support\Collection
    {
        $inTypes  = ['purchase','return_sale','opening','transfer_in','adjustment_in','asset_return'];
        $outTypes = ['sale','return_purchase','transfer_out','adjustment_out','scrap','asset_assign'];

        // موجودی فعلی هر کالا در هر انبار
        $currentStock = DB::table('stock_transactions as st')
            ->where('st.tenant_id',  $tenantId)
            ->where('st.company_id', $companyId)
            ->where('st.status',     'approved')
            ->groupBy('st.product_id','st.warehouse_id')
            ->selectRaw("st.product_id, st.warehouse_id,
                SUM(CASE WHEN st.type IN ('".implode("','", $inTypes)."') THEN st.quantity ELSE 0 END)
              - SUM(CASE WHEN st.type IN ('".implode("','", $outTypes)."') THEN st.quantity ELSE 0 END) AS stock")
            ->get()
            ->keyBy(fn($r) => "{$r->product_id}_{$r->warehouse_id}");

        $rules = ReorderRule::forTenant($tenantId, $companyId)
            ->active()
            ->with(['product','warehouse','preferredSupplier'])
            ->get();

        return $rules->filter(function ($rule) use ($currentStock) {
            $key   = "{$rule->product_id}_{$rule->warehouse_id}";
            $stock = $currentStock[$key]->stock ?? 0;
            return $stock <= $rule->reorder_point;
        })->map(function ($rule) use ($currentStock) {
            $key   = "{$rule->product_id}_{$rule->warehouse_id}";
            $stock = $currentStock[$key]->stock ?? 0;
            $rule->current_stock   = $stock;
            $rule->suggested_order = max($rule->reorder_quantity, $rule->reorder_point - $stock + $rule->safety_stock);
            return $rule;
        });
    }

    private function tenantCtx(): array
    {
        return [$this->manager->getTenantId(), $this->manager->getCompanyId()];
    }
}
