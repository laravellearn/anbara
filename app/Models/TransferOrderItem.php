<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransferOrderItem extends Model
{
    protected $fillable = [
        'transfer_order_id','product_id','measurement_unit_id',
        'quantity_requested','quantity_transferred',
        'unit_price','batch_number','serial_number','notes',
    ];

    protected $casts = [
        'quantity_requested'   => 'decimal:4',
        'quantity_transferred' => 'decimal:4',
        'unit_price'           => 'decimal:4',
    ];

    public function transferOrder()   { return $this->belongsTo(TransferOrder::class); }
    public function product()         { return $this->belongsTo(Product::class); }
    public function measurementUnit() { return $this->belongsTo(MeasurementUnit::class); }
}
