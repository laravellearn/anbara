<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseInvoiceItem extends Model
{
    protected $fillable = [

        'purchase_invoice_id',

        'product_id',

        'quantity',

        'unit_price',

        'discount_amount',

        'tax_amount',

        'total_amount',

        'description',
    ];

    protected $casts = [

        'quantity' => 'float',

        'unit_price' => 'decimal:0',

        'discount_amount' => 'decimal:0',

        'tax_amount' => 'decimal:0',

        'total_amount' => 'decimal:0',
    ];

    public function invoice()
    {
        return $this->belongsTo(
            PurchaseInvoice::class,
            'purchase_invoice_id'
        );
    }

    public function product()
    {
        return $this->belongsTo(
            Product::class
        );
    }
}