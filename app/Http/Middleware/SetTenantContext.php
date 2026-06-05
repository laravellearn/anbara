<?php

namespace App\Http\Middleware;

use Closure;
use App\Services\TenantManager;
use Illuminate\Support\Facades\Auth;

class SetTenantContext
{
    public function handle($request, Closure $next)
    {
        $manager = app(TenantManager::class);
        
        if (Auth::check()) {
            $user = Auth::user();
            $manager->setUser($user);

            // کاربر باید حتماً tenant_id داشته باشه (مگر ادمین کل)
            if ($user->tenant_id) {
                $tenant = \App\Models\Tenant::find($user->tenant_id);
                $manager->setTenant($tenant);
            }
            // اگر کاربر ادمین کل هست و tenant نداره، tenant همچنان null می‌مونه.
            // ممکنه بعداً از طریق پنل ادمین tenant مشخص بشه.
        }

        return $next($request);
    }
}