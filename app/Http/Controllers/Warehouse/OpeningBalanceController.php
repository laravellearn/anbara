<?php

namespace App\Http\Controllers\Warehouse;

use App\Enums\InventoryTransactionStatus;
use App\Enums\InventoryTransactionType;
use App\Models\MeasurementUnit;
use App\Models\Product;
use App\Models\StockTransaction;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class OpeningBalanceController extends BaseController
{
    /**
     * نمایش فرم ورود موجودی اولیه
     */
    public function index()
    {
        Gate::authorize('access', 'stock-transactions.create');

        $tenantId  = $this->manager->getTenantId();
        $companyId = $this->manager->getCompanyId();

        $warehouses = Warehouse::where('tenant_id', $tenantId)->where('is_active', true)->orderBy('title')->get();
        $products   = Product::where('tenant_id', $tenantId)->where('is_active', true)->orderBy('title')
            ->with('measurementUnit')
            ->get();

        // موجودی‌های اولیه ثبت‌شده
        $existing = StockTransaction::where('tenant_id', $tenantId)
            ->where('company_id', $companyId)
            ->where('type', InventoryTransactionType::OPENING)
            ->with(['product', 'warehouse', 'measurementUnit'])
            ->latest()
            ->get()
            ->groupBy('product_id');

        return view('warehouse.opening-balance.index', compact('warehouses', 'products', 'existing'));
    }

    /**
     * ذخیره موجودی اولیه — bulk (یک انبار، چند کالا)
     */
    public function store(Request $request)
    {
        Gate::authorize('access', 'stock-transactions.create');

        $request->validate([
            'warehouse_id'        => ['required', 'integer', 'exists:warehouses,id'],
            'items'               => ['required', 'array', 'min:1'],
            'items.*.product_id'  => ['required', 'integer', 'exists:products,id'],
            'items.*.quantity'    => ['required', 'numeric', 'min:0.0001'],
            'items.*.unit_price'  => ['nullable', 'numeric', 'min:0'],
        ]);

        $tenantId  = $this->manager->getTenantId();
        $companyId = $this->manager->getCompanyId();
        $userId    = auth()->id();

        // بررسی انبار متعلق به tenant
        $warehouse = Warehouse::where('id', $request->warehouse_id)
            ->where('tenant_id', $tenantId)
            ->firstOrFail();

        $saved = 0;
        $skipped = 0;

        DB::transaction(function () use ($request, $tenantId, $companyId, $userId, $warehouse, &$saved, &$skipped) {
            foreach ($request->items as $item) {
                if (empty($item['product_id']) || empty($item['quantity'])) {
                    $skipped++;
                    continue;
                }

                // جلوگیری از ثبت تکراری برای همین انبار + کالا
                $alreadyExists = StockTransaction::where('tenant_id', $tenantId)
                    ->where('company_id', $companyId)
                    ->where('type', InventoryTransactionType::OPENING)
                    ->where('warehouse_id', $warehouse->id)
                    ->where('product_id', $item['product_id'])
                    ->where('status', InventoryTransactionStatus::APPROVED)
                    ->exists();

                if ($alreadyExists) {
                    $skipped++;
                    continue;
                }

                StockTransaction::create([
                    'tenant_id'    => $tenantId,
                    'company_id'   => $companyId,
                    'user_id'      => $userId,
                    'warehouse_id' => $warehouse->id,
                    'product_id'   => $item['product_id'],
                    'quantity'     => $item['quantity'],
                    'unit_price'   => $item['unit_price'] ?? null,
                    'type'         => InventoryTransactionType::OPENING,
                    'status'       => InventoryTransactionStatus::APPROVED, // اتوماتیک تأیید
                    'description'  => 'موجودی اولیه — ' . now()->format('Y/m/d'),
                ]);
                $saved++;
            }
        });

        $msg = "موجودی اولیه ثبت شد: {$saved} ردیف ذخیره، {$skipped} ردیف رد/تکراری.";
        $type = $saved > 0 ? 'success' : 'warning';

        return redirect()->route('warehouse.opening-balance.index')->with('toast', [
            'message' => $msg,
            'type'    => $type,
            'title'   => 'ثبت موجودی اولیه',
        ]);
    }
}
