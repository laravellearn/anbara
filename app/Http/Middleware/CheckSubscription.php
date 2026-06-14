<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckSubscription
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        // ۱. اگر کاربر مهمان است → Middleware نباید اصلاً اجرا شود
        //    (ما این Middleware را فقط روی گروه 'auth' اعمال می‌کنیم)
        if (!$user) {
            return $next($request);
        }

        // ۲. سوپرادمین همیشه دسترسی کامل دارد
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        // ۳. اگر کاربر **مالک (Owner)** نباشد، یعنی یک کاربر عادی زیرمجموعه است.
        //    این کاربران بدون بررسی اشتراک عبور می‌کنند، زیرا مسئولیت پرداخت
        //    با مالک مستأجر است.
        if (!$user->isOwner()) {
            return $next($request);
        }

        // ۴. کاربر مالک است → باید Tenant و اشتراک فعال داشته باشد
        $tenant = $user->tenant;
        if (!$tenant || !$tenant->hasActiveSubscription()) {
            // اگر اشتراک فعال ندارد، فقط صفحه انتخاب پلن و وضعیت اشتراک در دسترس است
            if ($request->route()->getName() === 'plans.index') {
                return $next($request);
            }

            return redirect()->route('plans.index')->with(
                'error',
                'برای استفاده از امکانات، باید یک اشتراک فعال داشته باشید.'
            );
        }

        return $next($request);
    }
}
