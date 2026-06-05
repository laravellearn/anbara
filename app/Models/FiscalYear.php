<?php

namespace App\Models;

use App\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class FiscalYear extends Model
{
    use BelongsToTenant;

    protected $fillable = ['tenant_id', 'name', 'start_date', 'end_date', 'is_closed'];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'is_closed'  => 'boolean',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
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