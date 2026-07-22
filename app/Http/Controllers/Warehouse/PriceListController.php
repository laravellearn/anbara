<?php

namespace App\Http\Controllers\Warehouse;

use App\Models\PriceList;
use App\Models\PriceListItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class PriceListController extends BaseController
{
    // ─── لیست ────────────────────────────────────────────────────────────────
    public function index()
    {
        Gate::authorize('access', 'price-lists.view');
        [$tenantId, $companyId] = $this->tenantCtx();

        $priceLists = PriceList::forTenant($tenantId, $companyId)
            ->withCount('items')
            ->latest()
            ->paginate(20);

        $stats = [
            'total'  => PriceList::forTenant($tenantId, $companyId)->count(),
            'active' => PriceList::forTenant($tenantId, $companyId)->active()->count(),
        ];

        return view('warehouse.price-lists.index', compact('priceLists','stats'));
    }

    // ─── ایجاد ────────────────────────────────────────────────────────────────
    public function create()
    {
        Gate::authorize('access', 'price-lists.create');
        [$tenantId] = $this->tenantCtx();
        $products = Product::where('tenant_id', $tenantId)->where('is_active', true)->orderBy('title')->get();
        return view('warehouse.price-lists.create', compact('products'));
    }

    public function store(Request $request)
    {
        Gate::authorize('access', 'price-lists.create');
        [$tenantId, $companyId] = $this->tenantCtx();

        $request->validate([
            'name'                    => 'required|string|max:255',
            'type'                    => 'required|in:retail,wholesale,vip,special',
            'valid_from'              => 'nullable|date',
            'valid_to'                => 'nullable|date|after_or_equal:valid_from',
            'items'                   => 'required|array|min:1',
            'items.*.product_id'      => 'required|exists:products,id',
            'items.*.unit_price'      => 'required|numeric|min:0',
            'items.*.discount_percent'=> 'nullable|numeric|min:0|max:100',
        ]);

        DB::transaction(function () use ($request, $tenantId, $companyId) {
            $priceList = PriceList::create([
                'tenant_id'   => $tenantId,
                'company_id'  => $companyId,
                'name'        => $request->name,
                'type'        => $request->type,
                'description' => $request->description,
                'valid_from'  => $request->valid_from,
                'valid_to'    => $request->valid_to,
                'is_active'   => (bool) $request->is_active,
            ]);

            foreach ($request->items as $item) {
                if (empty($item['product_id'])) continue;
                PriceListItem::create([
                    'price_list_id'    => $priceList->id,
                    'product_id'       => $item['product_id'],
                    'unit_price'       => $item['unit_price'],
                    'min_quantity'     => $item['min_quantity'] ?? 1,
                    'discount_percent' => $item['discount_percent'] ?? 0,
                    'valid_from'       => $item['valid_from'] ?? null,
                    'valid_to'         => $item['valid_to'] ?? null,
                ]);
            }
        });

        return redirect()->route('warehouse.price-lists.index')
            ->with('success', 'لیست قیمت با موفقیت ایجاد شد.');
    }

    // ─── نمایش ────────────────────────────────────────────────────────────────
    public function show(PriceList $priceList)
    {
        Gate::authorize('access', 'price-lists.view');
        abort_if($priceList->tenant_id !== $this->manager->getTenantId(), 403);
        $priceList->load(['items.product']);
        return view('warehouse.price-lists.show', compact('priceList'));
    }

    // ─── ویرایش ───────────────────────────────────────────────────────────────
    public function edit(PriceList $priceList)
    {
        Gate::authorize('access', 'price-lists.create');
        abort_if($priceList->tenant_id !== $this->manager->getTenantId(), 403);
        [$tenantId] = $this->tenantCtx();
        $products = Product::where('tenant_id', $tenantId)->where('is_active', true)->orderBy('title')->get();
        $priceList->load('items.product');
        return view('warehouse.price-lists.edit', compact('priceList','products'));
    }

    public function update(Request $request, PriceList $priceList)
    {
        Gate::authorize('access', 'price-lists.create');
        abort_if($priceList->tenant_id !== $this->manager->getTenantId(), 403);

        $request->validate([
            'name'    => 'required|string|max:255',
            'type'    => 'required|in:retail,wholesale,vip,special',
            'valid_to'=> 'nullable|date',
            'items'   => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request, $priceList) {
            $priceList->update($request->only(['name','type','description','valid_from','valid_to','is_active']));

            // حذف آیتم‌های قبلی و درج مجدد
            $priceList->items()->delete();
            foreach ($request->items as $item) {
                if (empty($item['product_id'])) continue;
                PriceListItem::create([
                    'price_list_id'    => $priceList->id,
                    'product_id'       => $item['product_id'],
                    'unit_price'       => $item['unit_price'],
                    'min_quantity'     => $item['min_quantity'] ?? 1,
                    'discount_percent' => $item['discount_percent'] ?? 0,
                ]);
            }
        });

        return redirect()->route('warehouse.price-lists.show', $priceList)
            ->with('success', 'لیست قیمت به‌روزرسانی شد.');
    }

    // ─── حذف ─────────────────────────────────────────────────────────────────
    public function destroy(PriceList $priceList)
    {
        Gate::authorize('access', 'price-lists.delete');
        abort_if($priceList->tenant_id !== $this->manager->getTenantId(), 403);
        $priceList->delete();
        return back()->with('success', 'لیست قیمت حذف شد.');
    }

    // ─── API: قیمت محصول برای مشتری ──────────────────────────────────────────
    public function productPrice(Request $request)
    {
        [$tenantId, $companyId] = $this->tenantCtx();
        $productId   = $request->product_id;
        $priceListId = $request->price_list_id;
        $qty         = (float)($request->quantity ?? 1);

        $item = PriceListItem::whereHas('priceList', fn($q) =>
                $q->where('tenant_id', $tenantId)
                  ->where('company_id', $companyId)
                  ->where('id', $priceListId)
                  ->where('is_active', true))
            ->where('product_id', $productId)
            ->where('min_quantity', '<=', $qty)
            ->orderByDesc('min_quantity')
            ->first();

        return response()->json([
            'unit_price'      => $item?->unit_price ?? 0,
            'final_price'     => $item?->final_price ?? 0,
            'discount_percent'=> $item?->discount_percent ?? 0,
        ]);
    }

    private function tenantCtx(): array
    {
        return [$this->manager->getTenantId(), $this->manager->getCompanyId()];
    }
}
