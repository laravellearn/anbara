<?php

namespace App\Http\Middleware;

use Closure;
use App\Services\TenantManager;

class RequireTenant
{
    public function handle($request, Closure $next)
    {
        $manager = app(TenantManager::class);
        
        if (! $manager->getTenant()) {
            abort(403, 'دسترسی غیرمجاز - Tenant مشخص نشده است.');
        }

        return $next($request);
    }
}