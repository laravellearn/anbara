<?php

namespace App\Providers;

use App\Models\CompanyUser;
use App\Services\TenantManager;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Observers\UserObserver;

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
        $this->app->singleton(TenantManager::class);

        View::composer('*', function ($view) {
            $view->with('userLogin', Auth::user());
        });

        User::observe(UserObserver::class);

        //Permission
        Gate::define('access', function ($user, $permissionName) {
            $manager = app(TenantManager::class);
            $tenantId = $manager->getTenantId();
            $companyId = $manager->getCompanyId();

            if (!$tenantId || !$companyId) return false;

            // 1. چک دسترسی‌های مستقیم کاربر (permission_user) با شرط tenant
            $direct = $user->permissions()
                ->where('name', $permissionName)
                ->wherePivot('tenant_id', $tenantId)
                ->exists();
            if ($direct) return true;

            // 2. چک نقش‌های کاربر در این سازمان خاص
            $companyUser = CompanyUser::where('user_id', $user->id)
                ->where('company_id', $companyId)
                ->first();
            if (!$companyUser) return false;

            return $companyUser->roles()
                ->whereHas('permissions', fn($q) => $q->where('name', $permissionName))
                ->exists();
        });
    }
}
