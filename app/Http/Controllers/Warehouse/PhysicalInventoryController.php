<?php

namespace App\Http\Controllers\Warehouse;

use App\Models\PhysicalInventory;
use App\Models\PhysicalInventoryItem;
use App\Models\Warehouse;
use App\Models\Product;
use App\Models\StockTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class PhysicalInventoryController extends BaseController
{
    // ─── لیست ────────────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        Gate::authorize('access', 'physical-inventory.view');
        [$tenantId, $companyId] = $this->tenantCtx();

        $query = $this->fyQuery(PhysicalInventory::class, $tenantId, $companyId)
            ->with(['warehouse','creator'])->latest();

        if ($request->filled('status'))       $query->where('status', $request->status);
        if ($request->filled('warehouse_id')) $query->where('warehouse_id', $request->warehouse_id);

        $inventories = $query->paginate(20)->withQueryString();
        $warehouses  = Warehouse::where('tenant_id', $tenantId)->where('is_active', true)->get();
        $stats = [
            'total'    => $this->fyQuery(PhysicalInventory::class, $tenantId, $companyId)->count(),
            'counting' => $this->fyQuery(PhysicalInventory::class, $tenantId, $companyId)->where('status','counting')->count(),
            'adjusted' => $this->fyQuery(PhysicalInventory::class, $tenantId, $companyId)->where('status','adjusted')->count(),
        ];

        return view('warehouse.physical-inventory.index', compact('inventories','warehouses','stats'));
    }

    // ─── ایجاد + بارگذاری موجودی سیستمی ─────────────────────────────────────
    public function create()
    {
        Gate::authorize('access', 'physical-inventory.create');
        [$tenantId] = $this->tenantCtx();

        $warehouses = Warehouse::where('tenant_id', $tenantId)->where('is_active', true)->get();
        $number     = $this->generateNumber($tenantId);

        return view('warehouse.physical-inventory.create', compact('warehouses','number'));
    }

    public function store(Request $request)
    {
        Gate::authorize('access', 'physical-inventory.create');
        [$tenantId, $companyId] = $this->tenantCtx();

        $request->validate([
            'warehouse_id'   => 'required|exists:warehouses,id',
            'inventory_date' => 'required|date',
        ]);

        $inventory = DB::transaction(function () use ($request, $tenantId, $companyId) {
            $fy = $this->manager->getFiscalYear();

            $inventory = PhysicalInventory::create([
                'tenant_id'       => $tenantId,
                'company_id'      => $companyId,
                'fiscal_year_id'  => $fy?->id,
                'inventory_number'=> $this->generateNumber($tenantId),
                'status'          => 'counting',
                'warehouse_id'    => $request->warehouse_id,
                'inventory_date'  => $request->inventory_date,
                'notes'           => $request->notes,
                'created_by'      => auth()->id(),
            ]);

            // بارگذاری موجودی سیستمی فعلی
            $this->loadSystemStock($inventory, $tenantId, $companyId);

            return $inventory;
        });

        return redirect()->route('warehouse.physical-inventory.show', $inventory)
            ->with('success', 'فرم انبارگردانی ایجاد شد. شمارش فیزیکی را وارد کنید.');
    }

    // ─── نمایش + ورود شمارش ──────────────────────────────────────────────────
    public function show(PhysicalInventory $physicalInventory)
    {
        Gate::authorize('access', 'physical-inventory.view');
        $this->authorize($physicalInventory);

        $physicalInventory->load(['items.product','items.measurementUnit','warehouse','creator']);
        return view('warehouse.physical-inventory.show', compact('physicalInventory'));
    }

    // ─── ذخیره شمارش فیزیکی ──────────────────────────────────────────────────
    public function saveCounts(Request $request, PhysicalInventory $physicalInventory)
    {
        Gate::authorize('access', 'physical-inventory.create');
        $this->authorize($physicalInventory);

        if (!$physicalInventory->isEditable()) return back()->with('error', 'این انبارگردانی قابل ویرایش نیست.');

        DB::transaction(function () use ($request, $physicalInventory) {
            foreach ($request->input('counts', []) as $itemId => $qty) {
                $item = PhysicalInventoryItem::where('physical_inventory_id', $physicalInventory->id)
                    ->findOrFail($itemId);
                $item->counted_quantity = (float) $qty;
                $item->difference       = $item->counted_quantity - $item->system_quantity;
                $item->save();
            }
        });

        return back()->with('success', 'شمارش‌ها ذخیره شدند.');
    }

    // ─── تأیید و صدور سند تعدیل خودکار ──────────────────────────────────────
    public function adjust(PhysicalInventory $physicalInventory)
    {
        Gate::authorize('access', 'physical-inventory.adjust');
        $this->authorize($physicalInventory);

        if (!$physicalInventory->canAdjust()) return back()->with('error', 'وضعیت نامعتبر برای تعدیل.');

        $physicalInventory->load('items.product');

        DB::transaction(function () use ($physicalInventory) {
            $tenantId  = $physicalInventory->tenant_id;
            $companyId = $physicalInventory->company_id;
            $fyId      = $physicalInventory->fiscal_year_id;

            foreach ($physicalInventory->items as $item) {
                if ($item->counted_quantity === null || $item->difference == 0) continue;

                $isShortage = $item->difference < 0;
                $tx = StockTransaction::create([
                    'tenant_id'      => $tenantId,
                    'company_id'     => $companyId,
                    'fiscal_year_id' => $fyId,
                    'warehouse_id'   => $physicalInventory->warehouse_id,
                    'product_id'     => $item->product_id,
                    'measurement_unit_id' => $item->measurement_unit_id,
                    'type'           => $isShortage ? 'adjustment_out' : 'adjustment_in',
                    'status'         => 'approved',
                    'quantity'       => abs($item->difference),
                    'unit_price'     => $item->unit_price,
                    'description'    => "تعدیل انبارگردانی — {$physicalInventory->inventory_number}",
                    'reference_type' => PhysicalInventory::class,
                    'reference_id'   => $physicalInventory->id,
                    'user_id'        => auth()->id(),
                ]);

                $item->update(['adjustment_transaction_id' => $tx->id]);
            }

            $physicalInventory->update([
                'status'       => 'adjusted',
                'adjusted_at'  => now(),
                'completed_by' => auth()->id(),
            ]);
        });

        return back()->with('success', 'سند تعدیل خودکار صادر و موجودی‌ها اصلاح شدند.');
    }

    // ─── helpers ──────────────────────────────────────────────────────────────
    private function tenantCtx(): array
    {
        return [$this->manager->getTenantId(), $this->manager->getCompanyId()];
    }

    private function authorize(PhysicalInventory $inv): void
    {
        abort_if($inv->tenant_id !== $this->manager->getTenantId(), 403);
    }

    private function generateNumber(int $tenantId): string
    {
        $count = PhysicalInventory::where('tenant_id', $tenantId)->count() + 1;
        return 'PI-' . now()->format('Ym') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    /** بارگذاری موجودی سیستمی هر کالا در انبار انتخابی */
    private function loadSystemStock(PhysicalInventory $inventory, int $tenantId, int $companyId): void
    {
        $inTypes  = ['purchase','return_sale','opening','transfer_in','adjustment_in','asset_return'];
        $outTypes = ['sale','return_purchase','transfer_out','adjustment_out','scrap','asset_assign'];

        $stocks = DB::table('stock_transactions as st')
            ->join('products as p', 'p.id', '=', 'st.product_id')
            ->where('st.tenant_id',   $tenantId)
            ->where('st.company_id',  $companyId)
            ->where('st.warehouse_id',$inventory->warehouse_id)
            ->where('st.status',      'approved')
            ->where('p.is_active',    true)
            ->groupBy('p.id','st.measurement_unit_id')
            ->selectRaw("p.id as product_id, st.measurement_unit_id,
                MAX(st.unit_price) as unit_price,
                SUM(CASE WHEN st.type IN ('".implode("','", $inTypes)."') THEN st.quantity ELSE 0 END)
              - SUM(CASE WHEN st.type IN ('".implode("','", $outTypes)."') THEN st.quantity ELSE 0 END) AS system_qty")
            ->having('system_qty', '>', 0)
            ->get();

        foreach ($stocks as $s) {
            PhysicalInventoryItem::create([
                'physical_inventory_id' => $inventory->id,
                'product_id'            => $s->product_id,
                'measurement_unit_id'   => $s->measurement_unit_id,
                'system_quantity'       => $s->system_qty,
                'unit_price'            => $s->unit_price,
            ]);
        }
    }
}
