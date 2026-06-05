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
            // اگر tenant یا user نیست، شرکت رو نمی‌تونیم تنظیم کنیم
            // اما اینجا نباید error بدیم، چون ممکنه مسیر ادمین بدون شرکت باشه
            return $next($request);
        }

        // چک کن session مقدار داره یا نه
        $companyId = session('current_company_id');
        
        if ($companyId) {
            $company = Company::where('tenant_id', $tenant->id)
                ->where('id', $companyId)
                ->first();

            if ($company && $user->companies()->where('company_id', $company->id)->exists()) {
                $manager->setCompany($company);
            } else {
                // شرکت نامعتبر یا کاربر به آن دسترسی ندارد -> پاک کن و به حالت انتخاب هدایت کن
                session()->forget('current_company_id');
                // می‌تونی ریدایرکت کنی به صفحه انتخاب شرکت
                return redirect()->route('companies.select');
            }
        }

        return $next($request);
    }
}