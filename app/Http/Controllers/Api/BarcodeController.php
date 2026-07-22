<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\TenantManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class BarcodeController extends Controller
{
    public function __construct(private TenantManager $manager) {}

    /**
     * جستجوی کالا بر اساس بارکد / SKU
     */
    public function lookup(string $code): JsonResponse
    {
        $tenantId = $this->manager->getTenantId();

        $product = Product::where('tenant_id', $tenantId)
            ->where(fn($q) => $q->where('sku', $code)->orWhere('barcode', $code))
            ->with(['category', 'measurementUnit'])
            ->first();

        if (! $product) {
            return response()->json(['success' => false, 'message' => 'کالایی با این بارکد یافت نشد.'], 404);
        }

        return response()->json([
            'success' => true,
            'product' => [
                'id'            => $product->id,
                'title'         => $product->title,
                'sku'           => $product->sku,
                'barcode'       => $product->barcode ?? $product->sku,
                'category'      => $product->category?->title,
                'unit'          => $product->measurementUnit?->title ?? '—',
                'minimum_stock' => $product->minimum_stock,
            ],
        ]);
    }

    /**
     * موجودی لحظه‌ای کالا به تفکیک انبار
     */
    public function stock(string $code): JsonResponse
    {
        $tenantId  = $this->manager->getTenantId();
        $companyId = $this->manager->getCompanyId();

        $product = Product::where('tenant_id', $tenantId)
            ->where(fn($q) => $q->where('sku', $code)->orWhere('barcode', $code))
            ->first();

        if (! $product) {
            return response()->json(['success' => false, 'message' => 'کالایی با این بارکد یافت نشد.'], 404);
        }

        $stocks = DB::table('stock_transactions as st')
            ->join('warehouses as wh', 'wh.id', '=', 'st.warehouse_id')
            ->where('st.tenant_id',   $tenantId)
            ->where('st.company_id',  $companyId)
            ->where('st.product_id',  $product->id)
            ->where('st.status',      'approved')
            ->groupBy('wh.id', 'wh.title')
            ->select([
                'wh.id as warehouse_id',
                'wh.title as warehouse',
                DB::raw("SUM(CASE WHEN st.type IN ('purchase','return_sale','opening','transfer_in','adjustment_in','asset_return') THEN st.quantity ELSE 0 END)
                       - SUM(CASE WHEN st.type IN ('sale','return_purchase','transfer_out','adjustment_out','scrap','asset_assign') THEN st.quantity ELSE 0 END) AS current_stock"),
            ])
            ->get();

        return response()->json([
            'success'   => true,
            'product'   => ['id' => $product->id, 'title' => $product->title, 'sku' => $product->sku],
            'stocks'    => $stocks,
            'total'     => $stocks->sum('current_stock'),
        ]);
    }
}
