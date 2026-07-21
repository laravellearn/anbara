<?php

namespace App\Services;

use App\Enums\InventoryTransactionStatus;
use App\Enums\InventoryTransactionType;
use App\Models\StockTransaction;
use Illuminate\Support\Facades\DB;

/**
 * سرویس محاسبه موجودی انبار بصورت لایو از روی stock_transactions
 *
 * هیچ جدول کشی وجود ندارد — همه چیز مستقیم از تراکنش‌ها خوانده می‌شود.
 */
class StockService
{
    /**
     * موجودی یک کالا در یک انبار مشخص
     */
    public function getStock(int $productId, int $warehouseId, ?int $locationId = null): float
    {
        $query = $this->baseQuery()
            ->where('product_id', $productId)
            ->where('warehouse_id', $warehouseId);

        if ($locationId !== null) {
            $query->where('warehouse_location_id', $locationId);
        }

        return (float) $query->value('stock') ?? 0.0;
    }

    /**
     * موجودی یک کالا در تمام انبارهای یک شرکت
     * خروجی: collection از [warehouse_id, warehouse_title, quantity]
     */
    public function getStockByWarehouse(int $productId, int $tenantId, ?int $companyId = null): \Illuminate\Support\Collection
    {
        return DB::table('stock_transactions as st')
            ->join('warehouses as w', 'w.id', '=', 'st.warehouse_id')
            ->select(
                'st.warehouse_id',
                'w.title as warehouse_title',
                DB::raw('SUM(CASE WHEN ' . $this->inboundCondition() . ' THEN st.quantity ELSE -st.quantity END) as quantity')
            )
            ->where('st.product_id', $productId)
            ->where('st.tenant_id', $tenantId)
            ->where('st.status', InventoryTransactionStatus::APPROVED->value)
            ->when($companyId, fn($q) => $q->where('st.company_id', $companyId))
            ->whereNull('st.deleted_at')
            ->groupBy('st.warehouse_id', 'w.title')
            ->having('quantity', '!=', 0)
            ->get();
    }

    /**
     * موجودی کل یک کالا در تمام انبارها
     */
    public function getTotalStock(int $productId, int $tenantId, ?int $companyId = null): float
    {
        return (float) DB::table('stock_transactions')
            ->selectRaw('SUM(CASE WHEN ' . $this->inboundCondition() . ' THEN quantity ELSE -quantity END) as stock')
            ->where('product_id', $productId)
            ->where('tenant_id', $tenantId)
            ->where('status', InventoryTransactionStatus::APPROVED->value)
            ->when($companyId, fn($q) => $q->where('company_id', $companyId))
            ->whereNull('deleted_at')
            ->value('stock') ?? 0.0;
    }

    /**
     * لیست کالاها با موجودی لایو برای نمایش در صفحه انبار
     * خروجی: collection از [product_id, quantity, warehouse_id]
     */
    public function getStockList(int $tenantId, ?int $companyId = null, ?int $warehouseId = null): \Illuminate\Support\Collection
    {
        return DB::table('stock_transactions as st')
            ->join('products as p', 'p.id', '=', 'st.product_id')
            ->select(
                'st.product_id',
                'p.title as product_title',
                'p.sku',
                'st.warehouse_id',
                DB::raw('SUM(CASE WHEN ' . $this->inboundCondition() . ' THEN st.quantity ELSE -st.quantity END) as quantity')
            )
            ->where('st.tenant_id', $tenantId)
            ->where('st.status', InventoryTransactionStatus::APPROVED->value)
            ->when($companyId,  fn($q) => $q->where('st.company_id', $companyId))
            ->when($warehouseId, fn($q) => $q->where('st.warehouse_id', $warehouseId))
            ->whereNull('st.deleted_at')
            ->whereNull('p.deleted_at')
            ->groupBy('st.product_id', 'p.title', 'p.sku', 'st.warehouse_id')
            ->orderBy('p.title')
            ->get();
    }

    /**
     * کارتکس کالا — تمام حرکات یک کالا
     */
    public function getLedger(
        int $productId,
        int $tenantId,
        ?int $warehouseId = null,
        ?string $fromDate = null,
        ?string $toDate   = null
    ): \Illuminate\Database\Query\Builder {
        return DB::table('stock_transactions as st')
            ->join('warehouses as w', 'w.id', '=', 'st.warehouse_id')
            ->leftJoin('users as u', 'u.id', '=', 'st.user_id')
            ->select(
                'st.id',
                'st.created_at',
                'st.type',
                'st.status',
                'st.quantity',
                DB::raw('CASE WHEN ' . $this->inboundCondition() . ' THEN st.quantity ELSE -st.quantity END as net_quantity'),
                'st.unit_price',
                'st.batch_number',
                'st.serial_number',
                'st.description',
                'w.title as warehouse_title',
                'u.name as user_name',
                'st.reference_type',
                'st.reference_id'
            )
            ->where('st.product_id', $productId)
            ->where('st.tenant_id', $tenantId)
            ->where('st.status', InventoryTransactionStatus::APPROVED->value)
            ->when($warehouseId, fn($q) => $q->where('st.warehouse_id', $warehouseId))
            ->when($fromDate,    fn($q) => $q->whereDate('st.created_at', '>=', $fromDate))
            ->when($toDate,      fn($q) => $q->whereDate('st.created_at', '<=', $toDate))
            ->whereNull('st.deleted_at')
            ->orderBy('st.created_at');
    }

    /**
     * کالاهای زیر حداقل موجودی (هشدار)
     */
    public function getBelowMinimumStock(int $tenantId, ?int $companyId = null): \Illuminate\Support\Collection
    {
        return DB::table('stock_transactions as st')
            ->join('products as p', 'p.id', '=', 'st.product_id')
            ->select(
                'st.product_id',
                'p.title as product_title',
                'p.sku',
                'p.minimum_stock',
                'st.warehouse_id',
                DB::raw('SUM(CASE WHEN ' . $this->inboundCondition() . ' THEN st.quantity ELSE -st.quantity END) as current_stock')
            )
            ->where('st.tenant_id', $tenantId)
            ->where('st.status', InventoryTransactionStatus::APPROVED->value)
            ->when($companyId, fn($q) => $q->where('st.company_id', $companyId))
            ->whereNull('st.deleted_at')
            ->whereNull('p.deleted_at')
            ->where('p.is_active', true)
            ->groupBy('st.product_id', 'p.title', 'p.sku', 'p.minimum_stock', 'st.warehouse_id')
            ->havingRaw('current_stock < p.minimum_stock')
            ->orderBy('p.title')
            ->get();
    }

    // ─── private helpers ──────────────────────────────────────────────────────

    /**
     * Query پایه با محاسبه موجودی لایو
     */
    private function baseQuery(): \Illuminate\Database\Query\Builder
    {
        return DB::table('stock_transactions')
            ->selectRaw(
                'product_id, warehouse_id, warehouse_location_id,
                 SUM(CASE WHEN ' . $this->inboundCondition() . ' THEN quantity ELSE -quantity END) as stock'
            )
            ->where('status', InventoryTransactionStatus::APPROVED->value)
            ->whereNull('deleted_at')
            ->groupBy('product_id', 'warehouse_id', 'warehouse_location_id');
    }

    /**
     * شرط SQL برای تراکنش‌های ورودی (افزاینده موجودی)
     */
    private function inboundCondition(): string
    {
        $inbound = collect([
            InventoryTransactionType::OPENING,
            InventoryTransactionType::PURCHASE,
            InventoryTransactionType::TRANSFER_IN,
            InventoryTransactionType::ADJUSTMENT_IN,
            InventoryTransactionType::RETURN_SALE,
            InventoryTransactionType::ASSET_RETURN,
        ])->map(fn($t) => "'{$t->value}'")->implode(',');

        return "type IN ({$inbound})";
    }
}
