<?php

namespace App\Traits;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToTenant
{
    protected static function bootBelongsToTenant(): void
    {
        static::addGlobalScope('tenant', function (Builder $builder) {

            if (
                app()->bound('currentTenantId')
                && app('currentTenantId')
            ) {
                $builder->where(
                    $builder->qualifyColumn('tenant_id'),
                    app('currentTenantId')
                );
            }
        });

        static::creating(function ($model) {

            if (
                app()->bound('currentTenantId')
                && is_null($model->tenant_id)
            ) {
                $model->tenant_id = app('currentTenantId');
            }
        });
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function scopeForTenant(
        Builder $query,
        int $tenantId
    ): Builder {
        return $query->withoutGlobalScope('tenant')
            ->where('tenant_id', $tenantId);
    }
}