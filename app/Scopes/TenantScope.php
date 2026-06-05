<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use App\Services\TenantManager;

class TenantScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        if (property_exists($model, 'tenantIdColumn') && $model->tenantIdColumn) {
            $column = $model->tenantIdColumn;
        } else {
            $column = 'tenant_id';
        }

        $manager = app(TenantManager::class);
        if ($tenantId = $manager->getTenantId()) {
            $builder->where($model->getTable().'.'.$column, $tenantId);
        }
    }
}