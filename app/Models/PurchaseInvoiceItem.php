<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseInvoiceItem extends Model
{
    protected $fillable = [
        'purchase_invoice_id', 'purchase_order_item_id', 'product_id',
        'measurement_unit_id', 'quantity', 'unit_price',
        'discount_percent', 'description', 'sort_order',
    ];

    protected $casts = [
        'quantity'         => 'decimal:4',
        'unit_price'       => 'decimal:4',
        'discount_percent' => 'decimal:2',
    ];

    public function purchaseInvoice()
    {
        return $this->belongsTo(PurchaseInvoice::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function measurementUnit()
    {
        return $this->belongsTo(MeasurementUnit::class);
    }

    public function purchaseOrderItem()
    {
        return $this->belongsTo(PurchaseOrderItem::class);
    }

    public function getLineTotalAttribute(): float
    {
        return (float)$this->quantity * (float)$this->unit_price * (1 - (float)$this->discount_percent / 100);
    }
}
