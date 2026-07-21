<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuotationItem extends Model
{
    protected $fillable = [
        'quotation_id', 'product_id', 'measurement_unit_id',
        'quantity', 'unit_price', 'discount_amount', 'total_price',
        'description', 'sort_order',
    ];

    protected $casts = [
        'quantity'        => 'float',
        'unit_price'      => 'float',
        'discount_amount' => 'float',
        'total_price'     => 'float',
    ];

    public function quotation()       { return $this->belongsTo(Quotation::class); }
    public function product()         { return $this->belongsTo(Product::class); }
    public function measurementUnit() { return $this->belongsTo(MeasurementUnit::class); }
}
