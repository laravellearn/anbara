<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransferItem extends Model
{
    protected $fillable = [

        'transfer_id',

        'product_id',

        'warehouse_location_id',

        'quantity',

        'serial_number',

        'batch_number',

        'description'
    ];

    public function transfer()
    {
        return $this->belongsTo(
            Transfer::class
        );
    }

    public function product()
    {
        return $this->belongsTo(
            Product::class
        );
    }

    public function location()
    {
        return $this->belongsTo(
            WarehouseLocation::class,
            'warehouse_location_id'
        );
    }
}