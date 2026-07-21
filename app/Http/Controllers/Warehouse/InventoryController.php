<?php

namespace App\Http\Controllers\Warehouse;

use App\Models\Product;
use App\Models\Warehouse;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class InventoryController extends BaseController
{
    public function __construct(
        \App\Services\TenantManager $manager,
        private StockService $stockService
    ) {
        parent::__construct($manager);
    }

    /**
     * گزارش موجودی لایو — تمام کالاها × انبارها
     */
    public function index(Request $request)
    {
        Gate::authorize('access', 'inventory.view');

        $tenantId  = $this->manager->getTenantId();
        $companyId = $this->manager->getCompanyId();

        $warehouses = Warehouse::where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->orderBy('title')
            ->get();

        $stockList = $this->stockService->getStockList(
            $tenantId,
            $companyId,
            $request->filled('warehouse_id') ? (int)$request->warehouse_id : null
        );

        // فیلتر جستجو در سمت PHP (بعد از query)
        if ($request->filled('search')) {
            $s = mb_strtolower($request->search);
            $stockList = $stockList->filter(fn($row) =>
                str_contains(mb_strtolower($row->product_title), $s) ||
                str_contains(mb_strtolower($row->sku ?? ''), $s)
            );
        }

        // فیلتر هشدار موجودی
        if ($request->filled('alert')) {
            $productIds = Product::where('tenant_id', $tenantId)
                ->where('is_active', true)
                ->pluck('minimum_stock', 'id');

            $stockList = $stockList->filter(function ($row) use ($productIds) {
                $min = $productIds[$row->product_id] ?? 0;
                return (float)$row->quantity < (float)$min;
            });
        }

        $stats = [
            'total_products' => $stockList->pluck('product_id')->unique()->count(),
            'total_warehouses' => $warehouses->count(),
            'below_minimum'  => $this->stockService->getBelowMinimumStock($tenantId, $companyId)->count(),
            'zero_stock'     => $stockList->filter(fn($r) => (float)$r->quantity <= 0)->count(),
        ];

        return view('warehouse.inventory.index', compact(
            'stockList', 'warehouses', 'stats'
        ));
    }

    /**
     * کارتکس یک کالا — تمام حرکات با محاسبه تراز
     */
    public function ledger(Request $request, Product $product)
    {
        Gate::authorize('access', 'inventory.view');

        if ($product->tenant_id !== $this->manager->getTenantId()) {
            abort(403);
        }

        $tenantId   = $this->manager->getTenantId();
        $warehouses = Warehouse::where('tenant_id', $tenantId)->where('is_active', true)->orderBy('title')->get();

        $warehouseId = $request->filled('warehouse_id') ? (int)$request->warehouse_id : null;

        $transactions = $this->stockService->getLedger(
            $product->id,
            $tenantId,
            $warehouseId,
            $request->date_from,
            $request->date_to
        )->get();

        // محاسبه تراز تجمعی
        $balance = 0;
        $transactions = $transactions->map(function ($row) use (&$balance) {
            $balance += (float)$row->net_quantity;
            $row->balance = $balance;
            return $row;
        });

        $currentStock    = $product->currentStock($warehouseId);
        $stockByWarehouse = $product->stockByWarehouse();

        return view('warehouse.inventory.ledger', compact(
            'product', 'transactions', 'warehouses',
            'currentStock', 'stockByWarehouse'
        ));
    }

    /**
     * موجودی یک کالا به تفکیک انبار (AJAX / صفحه مستقل)
     */
    public function productStock(Product $product)
    {
        Gate::authorize('access', 'inventory.view');

        if ($product->tenant_id !== $this->manager->getTenantId()) {
            abort(403);
        }

        $stockByWarehouse = $product->stockByWarehouse();
        $totalStock       = $product->currentStock();

        if (request()->wantsJson()) {
            return response()->json([
                'product'         => $product->only('id', 'title', 'sku'),
                'total_stock'     => $totalStock,
                'stock_by_warehouse' => $stockByWarehouse,
            ]);
        }

        return view('warehouse.inventory.product-stock', compact('product', 'stockByWarehouse', 'totalStock'));
    }

    /**
     * کالاهای زیر حداقل موجودی
     */
    public function belowMinimum()
    {
        Gate::authorize('access', 'inventory.view');

        $tenantId  = $this->manager->getTenantId();
        $companyId = $this->manager->getCompanyId();

        $items = $this->stockService->getBelowMinimumStock($tenantId, $companyId);

        return view('warehouse.inventory.below-minimum', compact('items'));
    }
}
