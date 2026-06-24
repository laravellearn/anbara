<?php

namespace App\Models;

use App\Concerns\Auditable;
use App\Concerns\BelongsToTenant;
use App\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FiscalYear extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant,Auditable,LogsActivity;

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'is_active',
        'is_closed',
        'tenant_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'is_active'  => 'boolean',
        'is_closed'  => 'boolean',
    ];

    /**
     * مستأجر مرتبط
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * فعال‌سازی این سال مالی (فقط یک سال فعال در هر زمان)
     */
    public function activate(): void
    {
        $this->tenant->fiscalYears()->update(['is_active' => false]);
        $this->update(['is_active' => true]);
    }

    /**
     * بستن سال مالی
     */
    public function close(): void
    {
        $this->update(['is_closed' => true, 'is_active' => false]);
    }

    /**
     * سال مالی جاری Tenant را برمی‌گرداند (بر اساس تاریخ امروز)
     */
    public static function current(): ?self
    {
        $manager = app(\App\Services\TenantManager::class);
        $tenantId = $manager->getTenantId();
        if (!$tenantId) return null;

        return self::where('tenant_id', $tenantId)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->first();
    }
}