<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhysicalInventoryItem extends Model
{
    protected $fillable = [
        'physical_inventory_id','product_id','measurement_unit_id',
        'system_quantity','counted_quantity','difference',
        'unit_price','batch_number','notes','adjustment_transaction_id',
    ];

    protected $casts = [
        'system_quantity'  => 'decimal:4',
        'counted_quantity' => 'decimal:4',
        'difference'       => 'decimal:4',
        'unit_price'       => 'decimal:4',
    ];

    // محاسبه خودکار مغایرت
    public function setCountedQuantityAttribute($value): void
    {
        $this->attributes['counted_quantity'] = $value;
        $this->attributes['difference']       = $value - ($this->system_quantity ?? 0);
    }

    public function physicalInventory()  { return $this->belongsTo(PhysicalInventory::class); }
    public function product()            { return $this->belongsTo(Product::class); }
    public function measurementUnit()    { return $this->belongsTo(MeasurementUnit::class); }
    public function adjustmentTransaction() { return $this->belongsTo(StockTransaction::class, 'adjustment_transaction_id'); }
}
