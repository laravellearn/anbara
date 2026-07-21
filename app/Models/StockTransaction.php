<?php

namespace App\Models;

use App\Concerns\AutoFillTenantAndCompany;
use App\Concerns\BelongsToCompany;
use App\Concerns\BelongsToTenant;
use App\Enums\InventoryTransactionStatus;
use App\Enums\InventoryTransactionType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockTransaction extends Model
{
    use BelongsToTenant, BelongsToCompany, AutoFillTenantAndCompany, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'company_id',
        'fiscal_year_id',
        'cost_center_id',
        'warehouse_id',
        'warehouse_location_id',
        'product_id',
        'measurement_unit_id',
        'type',
        'status',
        'quantity',
        'unit_price',
        'batch_number',
        'expiry_date',
        'serial_number',
        'description',
        'user_id',
        'reference_type',
        'reference_id',
    ];

    protected $casts = [
        'type'        => InventoryTransactionType::class,
        'status'      => InventoryTransactionStatus::class,
        'quantity'    => 'decimal:4',
        'unit_price'  => 'decimal:4',
        'expiry_date' => 'date',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function fiscalYear()
    {
        return $this->belongsTo(FiscalYear::class);
    }

    public function costCenter()
    {
        return $this->belongsTo(CostCenter::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function warehouseLocation()
    {
        return $this->belongsTo(WarehouseLocation::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function measurementUnit()
    {
        return $this->belongsTo(MeasurementUnit::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * سند مبدا (فاکتور خرید/فروش، حواله انتقال، رسید تنظیم انبار و ...).
     * منطق کامل اتصال در فاز ماژول فاکتور تکمیل خواهد شد؛ این رابطه
     * از هم‌اکنون آماده است تا با ساخت مدل‌های مرتبط بدون تغییر در این مدل کار کند.
     */
    public function reference()
    {
        return $this->morphTo();
    }

    /**
     * آیا این تراکنش باعث افزایش موجودی می‌شود؟
     */
    public function isInbound(): bool
    {
        return in_array($this->type, [
            InventoryTransactionType::OPENING,
            InventoryTransactionType::PURCHASE,
            InventoryTransactionType::TRANSFER_IN,
            InventoryTransactionType::ADJUSTMENT_IN,
            InventoryTransactionType::RETURN_SALE,
            InventoryTransactionType::ASSET_RETURN,
        ], true);
    }

    /**
     * آیا این تراکنش باعث کاهش موجودی می‌شود؟
     */
    public function isOutbound(): bool
    {
        return ! $this->isInbound();
    }
}