<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\StockService;
use App\Services\TenantManager;
use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class InventoryApiController extends Controller
{
    public function __construct(
        private TenantManager $manager,
        private StockService $stockService
    ) {}

    /** موجودی کلی انبار */
    public function index(Request $request)
    {
        $tenantId    = $this->manager->getTenantId();
        $companyId   = $this->manager->getCompanyId();
        $warehouseId = $request->filled('warehouse_id') ? (int)$request->warehouse_id : null;

        $rows = $this->stockService->getStockList($tenantId, $companyId, $warehouseId);

        return response()->json(['data' => $rows]);
    }

    /** موجودی یک کالا در همه انبارها */
    public function product(int $productId)
    {
        $tenantId  = $this->manager->getTenantId();
        $companyId = $this->manager->getCompanyId();

        $product = Product::where('tenant_id', $tenantId)->findOrFail($productId);

        $stock = \Illuminate\Support\Facades\DB::table('stock_transactions')
            ->where('tenant_id', $tenantId)
            ->where('company_id', $companyId)
            ->where('product_id', $productId)
            ->where('status', 'approved')
            ->join('warehouses as wh', 'wh.id', '=', 'stock_transactions.warehouse_id')
            ->groupBy('warehouse_id', 'wh.title')
            ->selectRaw('warehouse_id, wh.title as warehouse,
                SUM(CASE WHEN type IN ("purchase_receipt","return_from_customer","opening","transfer_in","adjustment_in","receipt","return_in") THEN quantity ELSE 0 END)
              - SUM(CASE WHEN type IN ("issue","return_to_supplier","transfer_out","adjustment_out","return_out") THEN quantity ELSE 0 END) AS quantity')
            ->get();

        return response()->json([
            'product_id'    => $product->id,
            'product_title' => $product->title,
            'sku'           => $product->sku,
            'total_stock'   => $stock->sum('quantity'),
            'by_warehouse'  => $stock,
        ]);
    }

    /** کالاهای زیر حداقل */
    public function belowMinimum()
    {
        $tenantId  = $this->manager->getTenantId();
        $companyId = $this->manager->getCompanyId();

        $rows = \Illuminate\Support\Facades\DB::table('stock_transactions as st')
            ->join('products as p', 'p.id', '=', 'st.product_id')
            ->join('warehouses as wh', 'wh.id', '=', 'st.warehouse_id')
            ->where('st.tenant_id', $tenantId)->where('st.company_id', $companyId)
            ->where('st.status', 'approved')->where('p.minimum_stock', '>', 0)
            ->groupBy('p.id', 'p.title', 'p.minimum_stock', 'wh.id', 'wh.title')
            ->selectRaw('p.id, p.title as product, p.minimum_stock, wh.title as warehouse,
                SUM(CASE WHEN st.type IN ("purchase_receipt","return_from_customer","opening","transfer_in","adjustment_in","receipt","return_in") THEN st.quantity ELSE 0 END)
              - SUM(CASE WHEN st.type IN ("issue","return_to_supplier","transfer_out","adjustment_out","return_out") THEN st.quantity ELSE 0 END) AS current_stock')
            ->havingRaw('current_stock < p.minimum_stock')
            ->orderByRaw('(p.minimum_stock - current_stock) DESC')
            ->get();

        return response()->json(['data' => $rows]);
    }
}
