<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReturnInvoiceItem extends Model
{
    protected $fillable = [
        'return_invoice_id', 'product_id', 'measurement_unit_id',
        'quantity', 'unit_price', 'discount_percent', 'line_total',
        'serial_batch', 'description', 'sort_order',
    ];

    protected $casts = [
        'quantity'         => 'decimal:4',
        'unit_price'       => 'decimal:4',
        'discount_percent' => 'decimal:2',
        'line_total'       => 'decimal:4',
    ];

    public function returnInvoice()
    {
        return $this->belongsTo(ReturnInvoice::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function measurementUnit()
    {
        return $this->belongsTo(MeasurementUnit::class);
    }
}
