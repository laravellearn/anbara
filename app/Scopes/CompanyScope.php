<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use App\Services\TenantManager;

class CompanyScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $manager = app(TenantManager::class);
        $companyId = $manager->getCompanyId();

        // اگر company_id تنظیم نشده، هیچ رکوردی برنگردان تا از نشت داده جلوگیری شود
        if (!$companyId) {
            $builder->whereRaw('1 = 0');
            return;
        }

        $builder->where($model->getTable().'.company_id', $companyId);
    }
}