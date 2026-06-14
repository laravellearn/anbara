<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsOwner
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
        if (!auth()->check()) {
            abort(401, 'احراز هویت نشده');
        }

        if (!auth()->user()->isOwner()) {
            abort(403, 'شما دسترسی به این بخش را ندارید. تنها مالک سازمان می‌تواند این کار را انجام دهد.');
        }

        return $next($request);
    }
}
