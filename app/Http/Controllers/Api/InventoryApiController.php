<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StockTransaction;
use App\Models\Warehouse;
use App\Models\WarehouseDocument;
use App\Services\TenantManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryApiController extends Controller
{
    public function __construct(private TenantManager $manager) {}

    /**
     * موجودی لحظه‌ای همه کالاها
     */
    public function index(Request $request): JsonResponse
    {
        $tenantId  = $this->manager->getTenantId();
        $companyId = $this->manager->getCompanyId();

        $query = DB::table('stock_transactions as st')
            ->join('products as p',    'p.id',  '=', 'st.product_id')
            ->join('warehouses as wh', 'wh.id', '=', 'st.warehouse_id')
            ->leftJoin('measurement_units as mu', 'mu.id', '=', 'st.measurement_unit_id')
            ->where('st.tenant_id',  $tenantId)
            ->where('st.company_id', $companyId)
            ->where('st.status',     'approved')
            ->groupBy('p.id', 'p.title', 'p.sku', 'p.minimum_stock', 'wh.id', 'wh.title', 'mu.title')
            ->select([
                'p.id as product_id', 'p.title', 'p.sku', 'p.minimum_stock',
                'wh.id as warehouse_id', 'wh.title as warehouse', 'mu.title as unit',
                DB::raw("SUM(CASE WHEN st.type IN ('purchase','return_sale','opening','transfer_in','adjustment_in','asset_return') THEN st.quantity ELSE 0 END)
                       - SUM(CASE WHEN st.type IN ('sale','return_purchase','transfer_out','adjustment_out','scrap','asset_assign') THEN st.quantity ELSE 0 END) AS current_stock"),
            ]);

        if ($request->filled('warehouse_id')) {
            $query->where('st.warehouse_id', $request->warehouse_id);
        }

        $data = $query->orderBy('p.title')->get();

        return response()->json(['success' => true, 'data' => $data, 'count' => $data->count()]);
    }

    /**
     * لیست اسناد انبار
     */
    public function documents(Request $request): JsonResponse
    {
        $tenantId  = $this->manager->getTenantId();
        $companyId = $this->manager->getCompanyId();

        $docs = WarehouseDocument::where('tenant_id', $tenantId)
            ->where('company_id', $companyId)
            ->with(['warehouse'])
            ->latest('document_date')
            ->limit(50)
            ->get(['id', 'document_number', 'type', 'status', 'document_date', 'warehouse_id']);

        return response()->json(['success' => true, 'data' => $docs]);
    }
}
