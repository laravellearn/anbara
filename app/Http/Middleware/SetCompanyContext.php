<?php

namespace App\Http\Middleware;

use Closure;
use App\Services\TenantManager;
use App\Models\Company;
use App\Models\FiscalYear;

class SetCompanyContext
{
    public function handle($request, Closure $next)
    {
        $manager = app(TenantManager::class);
        $tenant  = $manager->getTenant();
        $user    = $manager->getUser();

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

                // ─── بارگذاری سال مالی فعال ───────────────────────────────
                $fyId = session('current_fiscal_year_id');
                if ($fyId) {
                    $fy = FiscalYear::where('company_id', $company->id)->find($fyId);
                } else {
                    $fy = FiscalYear::where('company_id', $company->id)
                        ->where('is_active', true)
                        ->first();
                    if ($fy) {
                        session(['current_fiscal_year_id' => $fy->id]);
                    }
                }
                $manager->setFiscalYear($fy ?? null);
            } else {
                session()->forget('current_company_id');

                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json(['error' => 'شرکت انتخابی نامعتبر است.'], 403);
                }

                return redirect()->route('companies.index');
            }
        }

        return $next($request);
    }
}