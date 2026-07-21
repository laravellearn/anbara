<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WarehouseDocumentItem extends Model
{
    protected $fillable = [
        'warehouse_document_id',
        'product_id', 'measurement_unit_id', 'warehouse_location_id',
        'quantity', 'unit_price',
        'serial_number', 'batch_number', 'expiry_date',
        'notes', 'received_quantity', 'stock_transaction_id', 'sort_order',
    ];

    protected $casts = [
        'expiry_date'       => 'date',
        'quantity'          => 'decimal:4',
        'unit_price'        => 'decimal:4',
        'received_quantity' => 'decimal:4',
    ];

    public function document()
    {
        return $this->belongsTo(WarehouseDocument::class, 'warehouse_document_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function measurementUnit()
    {
        return $this->belongsTo(MeasurementUnit::class);
    }

    public function warehouseLocation()
    {
        return $this->belongsTo(WarehouseLocation::class);
    }

    public function stockTransaction()
    {
        return $this->belongsTo(StockTransaction::class);
    }

    /** ارزش کل ردیف */
    public function getTotalValueAttribute(): float
    {
        return (float)$this->quantity * (float)($this->unit_price ?? 0);
    }
}
