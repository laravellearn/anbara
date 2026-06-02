<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Organization;

class IdentifyTenant
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // ۱. اگر کاربر مهمان است (لاگین نکرده) → هیچ scope ای اعمال نشود
        if (!auth()->check()) {
            $this->clearTenancy();
            return $next($request);
        }

        $user = auth()->user();

        // ۲. کاربر سوپرادمین (بدون tenant) → هیچ محدودیتی ندارد
        if ($user->isSuperAdmin()) {
            $this->clearTenancy();
            return $next($request);
        }

        // ۳. کاربر عادی که tenant_id دارد
        if ($user->tenant_id) {
            // ثبت مستأجر جاری در Container
            app()->instance('currentTenantId', $user->tenant_id);

            // ۴. تلاش برای تشخیص سازمان فعال کاربر
            $organizationId = $this->resolveOrganization($request, $user);
            // dd($organizationId, session()->all());


            if ($organizationId) {
                app()->instance('currentOrganizationId', $organizationId);
            } else {
                // اگر هیچ سازمانی وجود نداشته باشد، scope سازمان را پاک می‌کنیم
                // (اما tenant scope برقرار می‌ماند)
                app()->forgetInstance('currentOrganizationId');
                session()->forget('current_organization_id');

                // می‌توانید کاربر را به صفحه‌ای برای انتخاب/ایجاد سازمان هدایت کنید
                // اما اکنون فقط ادامه می‌دهیم
            }
        } else {
            // کاربر بدون tenant (نباید پیش بیاید اما در هر صورت پاکسازی)
            $this->clearTenancy();
        }

        return $next($request);
    }

    /**
     * تشخیص سازمان جاری کاربر (با اولویت session و سپس اولین سازمان)
     */
    protected function resolveOrganization(Request $request, $user): ?int
    {
        // الف) سازمان انتخاب‌شده در session
        $sessionOrgId = session('current_organization_id');

        if ($sessionOrgId) {
            // اعتبارسنجی: آیا کاربر واقعاً عضو این سازمان است و سازمان متعلق به tenant اوست؟
            $valid = $user->organizations()
                ->where('organization_user.organization_id', $sessionOrgId)
                ->where('organizations.tenant_id', $user->tenant_id)
                ->exists();

            if ($valid) {
                return $sessionOrgId;
            }

            // اگر نامعتبر است، session را پاک می‌کنیم
            session()->forget('current_organization_id');
        }

        // ب) انتخاب خودکار اولین سازمان معتبر کاربر
        $firstOrg = $user->organizations()
            ->where('organizations.tenant_id', $user->tenant_id)
            ->first();

        if ($firstOrg) {
            // آن را در session هم ذخیره می‌کنیم تا در درخواست‌های بعدی استفاده شود
            session(['current_organization_id' => $firstOrg->id]);
            return $firstOrg->id;
        }

        return null; // هیچ سازمانی ندارد
    }

    /**
     * پاک‌سازی کامل Tenant و Organization از Container
     */
    protected function clearTenancy(): void
    {
        app()->forgetInstance('currentTenantId');
        app()->forgetInstance('currentOrganizationId');
    }
}