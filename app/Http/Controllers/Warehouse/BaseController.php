<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Services\TenantManager;
use Illuminate\Database\Eloquent\Builder;

class BaseController extends Controller
{
    protected TenantManager $manager;

    public function __construct(TenantManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * ─── helper: اعمال فیلتر سال مالی روی query ──────────────────────────────
     * اگر سال مالی فعالی در session وجود دارد، فیلتر fiscal_year_id را اعمال می‌کند.
     * در غیر این صورت query دست‌نخورده برمی‌گردد.
     */
    protected function applyFiscalYear(Builder $query, string $column = 'fiscal_year_id'): Builder
    {
        $fy = $this->manager->getFiscalYear();
        if ($fy) {
            $query->where($column, $fy->id);
        }
        return $query;
    }

    /**
     * ─── helper: یک query جدید با فیلتر tenant+company+سال مالی ──────────────
     * استفاده: $this->fyQuery(SalesInvoice::class, $tenantId, $companyId)
     */
    protected function fyQuery(string $model, int $tenantId, ?int $companyId): Builder
    {
        /** @var Builder $q */
        $q = $model::forTenant($tenantId, $companyId);
        return $this->applyFiscalYear($q);
    }
}