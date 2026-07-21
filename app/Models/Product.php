<?php

namespace App\Models;

use App\Concerns\Auditable;
use App\Concerns\BelongsToTenant;
use App\Concerns\BelongsToCompany;
use App\Concerns\AutoFillTenantAndCompany;
use App\Concerns\LogsActivity;
use App\Enums\InventoryTransactionStatus;
use App\Enums\InventoryTransactionType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Product extends Model
{
    use BelongsToTenant, BelongsToCompany, AutoFillTenantAndCompany, SoftDeletes, Auditable, LogsActivity;

    protected $fillable = [
        'tenant_id',
        'company_id',
        'category_id',
        'brand_id',
        'product_type_id',
        'measurement_unit_id',
        'sku',
        'barcode',
        'title',
        'model',
        'part_number',
        'description',
        'minimum_stock',
        'maximum_stock',
        'is_asset',
        'is_active',
    ];

    protected $casts = [
        'minimum_stock' => 'decimal:4',
        'maximum_stock' => 'decimal:4',
        'is_asset' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function baseMeasurementUnit()
    {
        return $this->belongsTo(MeasurementUnit::class, 'measurement_unit_id');
    }

    public function measurementUnits()
    {
        return $this->belongsToMany(MeasurementUnit::class, 'product_measurement_units')
            ->withPivot('conversion_factor', 'is_default', 'company_id')
            ->withTimestamps();
    }

    public function attributeValues()
    {
        return $this->hasMany(ProductAttributeValue::class);
    }

    public function alternatives()
    {
        return $this->belongsToMany(Product::class, 'product_alternatives', 'product_id', 'alternative_product_id')
            ->withPivot('company_id')
            ->withTimestamps();
    }

    public function alternativeOf()
    {
        return $this->belongsToMany(Product::class, 'product_alternatives', 'alternative_product_id', 'product_id')
            ->withPivot('company_id')
            ->withTimestamps();
    }

    public function productType()
    {
        return $this->belongsTo(ProductType::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function stockTransactions()
    {
        return $this->hasMany(StockTransaction::class);
    }

    // ─── محاسبه موجودی لایو ───────────────────────────────────────────────────

    /**
     * موجودی کالا در یک انبار مشخص (یا تمام انبارها اگر null باشد)
     * داده مستقیم از stock_transactions خوانده می‌شود — بدون کش
     */
    public function currentStock(?int $warehouseId = null): float
    {
        $inbound = $this->inboundTypes();

        return (float) DB::table('stock_transactions')
            ->selectRaw(
                'COALESCE(SUM(CASE WHEN type IN (' . $inbound . ') THEN quantity ELSE -quantity END), 0) as stock'
            )
            ->where('product_id', $this->id)
            ->where('status', InventoryTransactionStatus::APPROVED->value)
            ->when($warehouseId, fn($q) => $q->where('warehouse_id', $warehouseId))
            ->whereNull('deleted_at')
            ->value('stock');
    }

    /**
     * موجودی به تفکیک انبار — برای نمایش در صفحه جزئیات کالا
     * خروجی: Collection از [warehouse_id, warehouse_title, quantity]
     */
    public function stockByWarehouse(): \Illuminate\Support\Collection
    {
        $inbound = $this->inboundTypes();

        return DB::table('stock_transactions as st')
            ->join('warehouses as w', 'w.id', '=', 'st.warehouse_id')
            ->selectRaw(
                'st.warehouse_id,
                 w.title as warehouse_title,
                 COALESCE(SUM(CASE WHEN st.type IN (' . $inbound . ') THEN st.quantity ELSE -st.quantity END), 0) as quantity'
            )
            ->where('st.product_id', $this->id)
            ->where('st.status', InventoryTransactionStatus::APPROVED->value)
            ->whereNull('st.deleted_at')
            ->groupBy('st.warehouse_id', 'w.title')
            ->orderBy('w.title')
            ->get();
    }

    /**
     * آیا موجودی زیر حداقل است؟
     */
    public function isBelowMinimumStock(?int $warehouseId = null): bool
    {
        return $this->currentStock($warehouseId) < (float) $this->minimum_stock;
    }

    /**
     * آیا موجودی بالای حداکثر است؟
     */
    public function isAboveMaximumStock(?int $warehouseId = null): bool
    {
        if ($this->maximum_stock === null) {
            return false;
        }

        return $this->currentStock($warehouseId) > (float) $this->maximum_stock;
    }

    // ─── private helper ───────────────────────────────────────────────────────

    private function inboundTypes(): string
    {
        return collect([
            InventoryTransactionType::OPENING,
            InventoryTransactionType::PURCHASE,
            InventoryTransactionType::TRANSFER_IN,
            InventoryTransactionType::ADJUSTMENT_IN,
            InventoryTransactionType::RETURN_SALE,
            InventoryTransactionType::ASSET_RETURN,
        ])->map(fn($t) => "'{$t->value}'")->implode(',');
    }
}
