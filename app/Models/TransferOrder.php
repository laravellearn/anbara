<?php

namespace App\Models;

use App\Concerns\AutoFillTenantAndCompany;
use App\Concerns\BelongsToCompany;
use App\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransferOrder extends Model
{
    use BelongsToTenant, BelongsToCompany, AutoFillTenantAndCompany, SoftDeletes;

    protected $fillable = [
        'tenant_id','company_id','fiscal_year_id','transfer_number','status',
        'from_warehouse_id','to_warehouse_id','transfer_date',
        'expected_arrival_date','actual_arrival_date',
        'reason','notes','created_by','confirmed_by','completed_by',
        'confirmed_at','completed_at',
    ];

    protected $casts = [
        'transfer_date'         => 'date',
        'expected_arrival_date' => 'date',
        'actual_arrival_date'   => 'date',
        'confirmed_at'          => 'datetime',
        'completed_at'          => 'datetime',
    ];

    const STATUS_DRAFT      = 'draft';
    const STATUS_CONFIRMED  = 'confirmed';
    const STATUS_IN_TRANSIT = 'in_transit';
    const STATUS_COMPLETED  = 'completed';
    const STATUS_CANCELLED  = 'cancelled';

    public static function statusLabels(): array
    {
        return [
            self::STATUS_DRAFT      => 'پیش‌نویس',
            self::STATUS_CONFIRMED  => 'تأیید شده',
            self::STATUS_IN_TRANSIT => 'در حال انتقال',
            self::STATUS_COMPLETED  => 'تکمیل شده',
            self::STATUS_CANCELLED  => 'لغو شده',
        ];
    }

    public static function statusColors(): array
    {
        return [
            self::STATUS_DRAFT      => 'secondary',
            self::STATUS_CONFIRMED  => 'primary',
            self::STATUS_IN_TRANSIT => 'warning',
            self::STATUS_COMPLETED  => 'success',
            self::STATUS_CANCELLED  => 'danger',
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statusLabels()[$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute(): string
    {
        return self::statusColors()[$this->status] ?? 'secondary';
    }

    public function isEditable(): bool    { return $this->status === self::STATUS_DRAFT; }
    public function canConfirm(): bool    { return $this->status === self::STATUS_DRAFT; }
    public function canTransit(): bool    { return $this->status === self::STATUS_CONFIRMED; }
    public function canComplete(): bool   { return in_array($this->status, [self::STATUS_CONFIRMED, self::STATUS_IN_TRANSIT]); }
    public function canCancel(): bool     { return in_array($this->status, [self::STATUS_DRAFT, self::STATUS_CONFIRMED]); }

    public function items()         { return $this->hasMany(TransferOrderItem::class)->orderBy('id'); }
    public function fromWarehouse() { return $this->belongsTo(Warehouse::class, 'from_warehouse_id'); }
    public function toWarehouse()   { return $this->belongsTo(Warehouse::class, 'to_warehouse_id'); }
    public function fiscalYear()    { return $this->belongsTo(FiscalYear::class); }
    public function creator()       { return $this->belongsTo(User::class, 'created_by'); }
    public function confirmer()     { return $this->belongsTo(User::class, 'confirmed_by'); }
    public function completer()     { return $this->belongsTo(User::class, 'completed_by'); }

    public function scopeForTenant($q, int $tenantId, int $companyId)
    {
        return $q->where('tenant_id', $tenantId)->where('company_id', $companyId);
    }
}
