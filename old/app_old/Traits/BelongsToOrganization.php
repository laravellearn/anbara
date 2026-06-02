<?php

namespace App\Traits;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToOrganization
{
    protected static function bootBelongsToOrganization(): void
    {
        static::addGlobalScope('organization', function (Builder $builder) {

            if (
                app()->bound('currentOrganizationId')
                && app('currentOrganizationId')
            ) {
                $builder->where(
                    $builder->qualifyColumn('organization_id'),
                    app('currentOrganizationId')
                );
            }
        });

        static::creating(function ($model) {

            if (
                app()->bound('currentOrganizationId')
                && is_null($model->organization_id)
            ) {
                $model->organization_id = app('currentOrganizationId');
            }
        });
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function scopeForOrganization(
        Builder $query,
        int $organizationId
    ): Builder {
        return $query->withoutGlobalScope('organization')
            ->where('organization_id', $organizationId);
    }
}