<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        // ۱. سوپرادمین همیشه دسترسی آزاد دارد
        if ($user && $user->isSuperAdmin()) {
            return $next($request);
        }

        $tenant = $request->user()?->tenant;
        if (!$tenant || !$tenant->hasActiveSubscription()) {
            return redirect()->route('plans.index')->with('error', 'برای استفاده از این بخش، باید اشتراک فعال داشته باشید.');
        }
        return $next($request);
    }
}
