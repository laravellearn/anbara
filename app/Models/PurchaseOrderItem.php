<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrderItem extends Model
{
    protected $fillable = [
        'purchase_order_id', 'product_id', 'measurement_unit_id',
        'quantity_ordered', 'unit_price', 'discount_percent',
        'quantity_received', 'description', 'expected_delivery_date',
        'warehouse_document_id', 'sort_order',
    ];

    protected $casts = [
        'expected_delivery_date' => 'date',
        'quantity_ordered'       => 'decimal:4',
        'unit_price'             => 'decimal:4',
        'discount_percent'       => 'decimal:2',
        'quantity_received'      => 'decimal:4',
    ];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function measurementUnit()
    {
        return $this->belongsTo(MeasurementUnit::class);
    }

    public function warehouseDocument()
    {
        return $this->belongsTo(WarehouseDocument::class);
    }

    /** ارزش ردیف بعد از تخفیف */
    public function getLineTotalAttribute(): float
    {
        return (float)$this->quantity_ordered
            * (float)($this->unit_price ?? 0)
            * (1 - (float)$this->discount_percent / 100);
    }

    /** مقدار باقی‌مانده برای دریافت */
    public function getRemainingQtyAttribute(): float
    {
        return max(0, (float)$this->quantity_ordered - (float)$this->quantity_received);
    }

    public function isFullyReceived(): bool
    {
        return (float)$this->quantity_received >= (float)$this->quantity_ordered;
    }
}
