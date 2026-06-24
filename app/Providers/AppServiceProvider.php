<?php

namespace App\Providers;

use App\Models\Company;
use App\Models\Employee;
use App\Models\Permission;
use App\Models\Product;
use App\Services\TenantManager;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Models\Warehouse;
use App\Observers\CompanyObserver;
use App\Observers\EmployeeObserver;
use App\Observers\ProductObserver;
use App\Observers\UserObserver;
use App\Observers\WarehouseObserver;
use App\Services\PlanService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        Product::observe(ProductObserver::class);
        Warehouse::observe(WarehouseObserver::class);
        User::observe(UserObserver::class);
        Employee::observe(EmployeeObserver::class);     // جدید
        Company::observe(CompanyObserver::class);        // جدید

        $this->app->singleton(TenantManager::class);

        View::composer('*', function ($view) {
            $view->with('userLogin', Auth::user());
        });


        View::composer('*', function ($view) {
            if (Auth::check()) {
                $user = Auth::user();

                $view->with([
                    'currentRole' => $user->getCurrentRole(),
                    'currentRoleName' => $user->getCurrentRoleName(),
                    'allRoles' => $user->getRolesWithCompanies(),
                    'isSuperAdmin' => $user->isSuperAdmin(),
                ]);
            } else {
                $view->with([
                    'currentRole' => null,
                    'currentRoleName' => 'مهمان',
                    'allRoles' => collect(),
                    'isSuperAdmin' => false,
                ]);
            }
        });


        User::observe(UserObserver::class);

        //Permission
        Gate::define('access', function (?User $user, string $permissionName) {
            if (!$user) {
                return false;
            }

            // Super Admin (کاربر بدون Tenant) به همه چیز دسترسی دارد
            if ($user->isSuperAdmin()) {
                return true;
            }

            // مدیر سازمان (tenant_admin) در شرکت جاری به همه چیز دسترسی دارد
            if ($user->isTenantAdmin()) {
                return true;
            }

            $manager = app(TenantManager::class);
            $tenantId = $manager->getTenantId();
            $companyId = $manager->getCompanyId();

            if (!$tenantId || !$companyId) {
                return false;
            }

            // چک دسترسی مستقیم کاربر (permission_user)
            $direct = $user->permissions()
                ->where('name', $permissionName)
                ->wherePivot('tenant_id', $tenantId)
                ->exists();

            if ($direct) {
                return true;
            }

            // چک دسترسی از طریق نقش‌ها
            $companyUser = \App\Models\CompanyUser::where('user_id', $user->id)
                ->where('company_id', $companyId)
                ->first();

            if (!$companyUser) {
                return false;
            }

            return $companyUser->roles()
                ->whereHas('permissions', function ($query) use ($permissionName) {
                    $query->where('name', $permissionName);
                })
                ->exists();
        });

        // تعریف یک Gate اختصاصی به ازای هر Permission موجود
        try {
            $permissions = Permission::all()->pluck('name');
            foreach ($permissions as $permission) {
                Gate::define($permission, function (User $user) use ($permission) {
                    return Gate::allows('access', $permission);
                });
            }
        } catch (\Exception $e) {
            // اگر جدول permissions هنوز وجود نداشته باشد (مثلاً هنگام migrate)
        }


        Gate::define('feature', function (User $user, string $featureKey) {
            $planService = app(PlanService::class);
            return $planService->tenantHasFeature($featureKey);
        });
    }
}
