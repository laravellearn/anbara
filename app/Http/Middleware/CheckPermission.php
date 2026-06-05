<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Gate;

class CheckPermission
{
    public function handle($request, Closure $next, $permission)
    {
        if (Gate::denies('access', $permission)) {
            abort(403, 'شما دسترسی لازم برای این عملیات را ندارید.');
        }

        return $next($request);
    }
}