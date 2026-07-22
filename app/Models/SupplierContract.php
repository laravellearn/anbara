<?php

namespace App\Models;

use App\Concerns\AutoFillTenantAndCompany;
use App\Concerns\BelongsToCompany;
use App\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupplierContract extends Model
{
    use BelongsToTenant, BelongsToCompany, AutoFillTenantAndCompany, SoftDeletes;

    protected $fillable = [
        'tenant_id','company_id','fiscal_year_id','contract_number','status',
        'supplier_id','title','start_date','end_date',
        'credit_limit','payment_terms_days','discount_percent',
        'terms_and_conditions','notes','file_path','created_by',
    ];

    protected $casts = [
        'start_date'          => 'date',
        'end_date'            => 'date',
        'credit_limit'        => 'decimal:2',
        'payment_terms_days'  => 'integer',
        'discount_percent'    => 'decimal:2',
    ];

    const STATUS_DRAFT      = 'draft';
    const STATUS_ACTIVE     = 'active';
    const STATUS_EXPIRED    = 'expired';
    const STATUS_TERMINATED = 'terminated';

    public static function statusLabels(): array
    {
        return [
            self::STATUS_DRAFT      => 'پیش‌نویس',
            self::STATUS_ACTIVE     => 'فعال',
            self::STATUS_EXPIRED    => 'منقضی',
            self::STATUS_TERMINATED => 'فسخ شده',
        ];
    }

    public static function statusColors(): array
    {
        return [
            self::STATUS_DRAFT      => 'secondary',
            self::STATUS_ACTIVE     => 'success',
            self::STATUS_EXPIRED    => 'warning',
            self::STATUS_TERMINATED => 'danger',
        ];
    }

    public function getStatusLabelAttribute(): string { return self::statusLabels()[$this->status] ?? $this->status; }
    public function getStatusColorAttribute(): string { return self::statusColors()[$this->status] ?? 'secondary'; }

    public function isExpired(): bool { return $this->end_date && $this->end_date->isPast(); }

    public function supplier()   { return $this->belongsTo(Contact::class, 'supplier_id'); }
    public function fiscalYear() { return $this->belongsTo(FiscalYear::class); }
    public function creator()    { return $this->belongsTo(User::class, 'created_by'); }

    public function scopeForTenant($q, int $tenantId, int $companyId)
    {
        return $q->where('tenant_id', $tenantId)->where('company_id', $companyId);
    }

    public function scopeActive($q)  { return $q->where('status', self::STATUS_ACTIVE); }
    public function scopeExpiring($q, int $days = 30)
    {
        return $q->where('status', self::STATUS_ACTIVE)
                 ->whereBetween('end_date', [now(), now()->addDays($days)]);
    }
}
