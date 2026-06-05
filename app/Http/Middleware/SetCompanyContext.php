<?php

namespace App\Http\Middleware;

use Closure;
use App\Services\TenantManager;
use App\Models\Company;

class SetCompanyContext
{
    public function handle($request, Closure $next)
    {
        $manager = app(TenantManager::class);
        $tenant = $manager->getTenant();
        $user = $manager->getUser();

        if (! $tenant || ! $user) {
            return $next($request);
        }

        $companyId = session('current_company_id');

        if ($companyId) {
            $company = Company::where('tenant_id', $tenant->id)
                ->where('id', $companyId)
                ->first();

            if ($company && $user->companies()->where('company_id', $company->id)->exists()) {
                $manager->setCompany($company);
            } else {
                session()->forget('current_company_id');
                return redirect()->route('companies.select'); // اگر صفحه انتخاب شرکت دارید
            }
        }

        return $next($request);
    }
}