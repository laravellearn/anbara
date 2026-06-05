<?php

namespace App\Concerns;

use App\Services\TenantManager;

/**
 * @mixin \Illuminate\Database\Eloquent\Model
 */
trait AutoFillTenantAndCompany
{
    protected static function bootAutoFillTenantAndCompany(): void
    {
        static::creating(function ($model) {
            $manager = app(TenantManager::class);

            if (in_array('tenant_id', $model->getFillable()) && empty($model->tenant_id)) {
                $model->tenant_id = $manager->getTenantId();
            }

            if (in_array('company_id', $model->getFillable()) && empty($model->company_id)) {
                $model->company_id = $manager->getCompanyId();
            }
        });
    }
}