<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SerialBatch extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id', 'company_id', 'product_id', 'warehouse_id', 'warehouse_document_id',
        'tracking_type', 'serial_number', 'batch_number',
        'manufacture_date', 'expiry_date', 'quantity', 'status', 'notes', 'created_by',
    ];

    protected $casts = [
        'manufacture_date' => 'date',
        'expiry_date'      => 'date',
        'quantity'         => 'float',
    ];

    // ─── Relations ────────────────────────────────────────────────────────
    public function product()           { return $this->belongsTo(Product::class); }
    public function warehouse()         { return $this->belongsTo(Warehouse::class); }
    public function warehouseDocument() { return $this->belongsTo(WarehouseDocument::class); }
    public function creator()           { return $this->belongsTo(User::class, 'created_by'); }

    // ─── Scopes ───────────────────────────────────────────────────────────
    public function scopeExpiringSoon($q, int $days = 30)
    {
        return $q->whereNotNull('expiry_date')
                 ->whereDate('expiry_date', '>=', now())
                 ->whereDate('expiry_date', '<=', now()->addDays($days));
    }

    public function scopeExpired($q)
    {
        return $q->whereNotNull('expiry_date')->whereDate('expiry_date', '<', now());
    }

    // ─── Static labels / colors ───────────────────────────────────────────
    public static function statusLabels(): array
    {
        return [
            'in_stock' => 'در انبار',
            'issued'   => 'صادر شده',
            'returned' => 'مرجوعی',
            'scrapped' => 'اسقاط',
        ];
    }

    public static function statusColors(): array
    {
        return [
            'in_stock' => 'success',
            'issued'   => 'primary',
            'returned' => 'warning',
            'scrapped' => 'danger',
        ];
    }
}
