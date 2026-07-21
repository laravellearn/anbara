<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseRequestItem extends Model
{
    protected $fillable = [
        'purchase_request_id', 'product_id', 'measurement_unit_id',
        'quantity_requested', 'estimated_unit_price', 'description', 'sort_order',
    ];

    protected $casts = [
        'quantity_requested'   => 'decimal:4',
        'estimated_unit_price' => 'decimal:4',
    ];

    public function purchaseRequest()
    {
        return $this->belongsTo(PurchaseRequest::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function measurementUnit()
    {
        return $this->belongsTo(MeasurementUnit::class);
    }

    public function getLineTotalAttribute(): float
    {
        return (float)$this->quantity_requested * (float)($this->estimated_unit_price ?? 0);
    }
}
