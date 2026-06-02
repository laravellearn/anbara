<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryTransactionItem extends Model
{
    protected $fillable = [

        'inventory_transaction_id',

        'product_id',

        'warehouse_location_id',

        'quantity',

        'unit_price',

        'total_price',

        'serial_number',

        'batch_number',

        'expire_date',

        'description',
    ];

    protected $casts = [

        'quantity' => 'float',

        'unit_price' => 'decimal:0',

        'total_price' => 'decimal:0',

        'expire_date' => 'date',
    ];

    public function transaction()
    {
        return $this->belongsTo(
            InventoryTransaction::class,
            'inventory_transaction_id'
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