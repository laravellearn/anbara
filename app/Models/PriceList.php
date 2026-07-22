<?php

namespace App\Models;

use App\Concerns\AutoFillTenantAndCompany;
use App\Concerns\BelongsToCompany;
use App\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PriceList extends Model
{
    use BelongsToTenant, BelongsToCompany, AutoFillTenantAndCompany, SoftDeletes;

    protected $fillable = [
        'tenant_id','company_id','name','type',
        'description','valid_from','valid_to','is_active',
    ];

    protected $casts = [
        'valid_from' => 'date',
        'valid_to'   => 'date',
        'is_active'  => 'boolean',
    ];

    public static function typeLabels(): array
    {
        return [
            'retail'    => 'خرده‌فروشی',
            'wholesale' => 'عمده‌فروشی',
            'vip'       => 'مشتریان VIP',
            'special'   => 'قیمت ویژه',
        ];
    }

    public static function typeColors(): array
    {
        return [
            'retail'    => 'secondary',
            'wholesale' => 'info',
            'vip'       => 'warning',
            'special'   => 'danger',
        ];
    }

    public function getTypeLabelAttribute(): string { return self::typeLabels()[$this->type] ?? $this->type; }
    public function getTypeColorAttribute(): string { return self::typeColors()[$this->type] ?? 'secondary'; }

    public function isValid(): bool
    {
        $now = now()->toDateString();
        return $this->is_active
            && (!$this->valid_from || $this->valid_from->lte(now()))
            && (!$this->valid_to   || $this->valid_to->gte(now()));
    }

    public function items() { return $this->hasMany(PriceListItem::class)->with('product'); }

    public function scopeForTenant($q, int $tenantId, int $companyId)
    {
        return $q->where('tenant_id', $tenantId)->where('company_id', $companyId);
    }

    public function scopeActive($q) { return $q->where('is_active', true); }
}
