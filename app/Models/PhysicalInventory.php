<?php

namespace App\Models;

use App\Concerns\AutoFillTenantAndCompany;
use App\Concerns\BelongsToCompany;
use App\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PhysicalInventory extends Model
{
    use BelongsToTenant, BelongsToCompany, AutoFillTenantAndCompany, SoftDeletes;

    protected $fillable = [
        'tenant_id','company_id','fiscal_year_id','inventory_number','status',
        'warehouse_id','inventory_date','notes',
        'created_by','completed_by','completed_at','adjusted_at',
    ];

    protected $casts = [
        'inventory_date' => 'date',
        'completed_at'   => 'datetime',
        'adjusted_at'    => 'datetime',
    ];

    const STATUS_DRAFT     = 'draft';
    const STATUS_COUNTING  = 'counting';
    const STATUS_COMPLETED = 'completed';
    const STATUS_ADJUSTED  = 'adjusted';
    const STATUS_CANCELLED = 'cancelled';

    public static function statusLabels(): array
    {
        return [
            self::STATUS_DRAFT     => 'پیش‌نویس',
            self::STATUS_COUNTING  => 'در حال شمارش',
            self::STATUS_COMPLETED => 'شمارش کامل',
            self::STATUS_ADJUSTED  => 'تعدیل شده',
            self::STATUS_CANCELLED => 'لغو شده',
        ];
    }

    public static function statusColors(): array
    {
        return [
            self::STATUS_DRAFT     => 'secondary',
            self::STATUS_COUNTING  => 'info',
            self::STATUS_COMPLETED => 'warning',
            self::STATUS_ADJUSTED  => 'success',
            self::STATUS_CANCELLED => 'danger',
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

    public function isEditable(): bool     { return in_array($this->status, [self::STATUS_DRAFT, self::STATUS_COUNTING]); }
    public function canComplete(): bool    { return $this->status === self::STATUS_COUNTING; }
    public function canAdjust(): bool      { return $this->status === self::STATUS_COMPLETED; }
    public function canCancel(): bool      { return in_array($this->status, [self::STATUS_DRAFT, self::STATUS_COUNTING]); }

    // مجموع مغایرت
    public function getTotalDifferenceAttribute(): float
    {
        return (float) $this->items->sum('difference');
    }

    public function items()    { return $this->hasMany(PhysicalInventoryItem::class); }
    public function warehouse(){ return $this->belongsTo(Warehouse::class); }
    public function fiscalYear(){ return $this->belongsTo(FiscalYear::class); }
    public function creator()  { return $this->belongsTo(User::class, 'created_by'); }
    public function completer(){ return $this->belongsTo(User::class, 'completed_by'); }

    public function scopeForTenant($q, int $tenantId, int $companyId)
    {
        return $q->where('tenant_id', $tenantId)->where('company_id', $companyId);
    }
}
