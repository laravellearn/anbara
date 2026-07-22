<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PriceListItem extends Model
{
    protected $fillable = [
        'price_list_id','product_id','unit_price',
        'min_quantity','discount_percent','valid_from','valid_to',
    ];

    protected $casts = [
        'unit_price'       => 'decimal:2',
        'min_quantity'     => 'decimal:4',
        'discount_percent' => 'decimal:2',
        'valid_from'       => 'date',
        'valid_to'         => 'date',
    ];

    public function getFinalPriceAttribute(): float
    {
        return round($this->unit_price * (1 - $this->discount_percent / 100), 2);
    }

    public function priceList() { return $this->belongsTo(PriceList::class); }
    public function product()   { return $this->belongsTo(Product::class); }
}
