<?php

namespace App\Concerns;

use App\Services\TenantManager;

trait AutoFillTenantAndCompany
{
    protected static function bootAutoFillTenantAndCompany(): void
    {
        static::creating(function ($model) {
            $manager = app(TenantManager::class);

            // اگر tenant_id جزو فیلدهای fillable مدل بود و خالی بود، مقداردهی کن
            if (in_array('tenant_id', $model->getFillable()) && empty($model->tenant_id)) {
                $model->tenant_id = $manager->getTenantId();
            }

            // اگر company_id جزو فیلدهای fillable مدل بود و خالی بود، مقداردهی کن
            if (in_array('company_id', $model->getFillable()) && empty($model->company_id)) {
                $model->company_id = $manager->getCompanyId();
            }
        });
    }
}