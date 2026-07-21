<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemRequestItem extends Model
{
    protected $fillable = [
        'item_request_id', 'product_id', 'measurement_unit_id',
        'quantity_requested', 'quantity_issued', 'description', 'sort_order',
    ];

    protected $casts = [
        'quantity_requested' => 'decimal:4',
        'quantity_issued'    => 'decimal:4',
    ];

    public function itemRequest()
    {
        return $this->belongsTo(ItemRequest::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function measurementUnit()
    {
        return $this->belongsTo(MeasurementUnit::class);
    }

    public function getRemainingAttribute(): float
    {
        return (float)$this->quantity_requested - (float)$this->quantity_issued;
    }
}
