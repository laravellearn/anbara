<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * این Provider برای سیستم چند-مستأجری سفارشی (custom multi-tenancy) نگه‌داشته شده است.
 * پروژه از پکیج stancl/tenancy استفاده نمی‌کند — ایزوله‌سازی تننت‌ها از طریق
 * TenantManager، TenantScope، و Middleware های اختصاصی انجام می‌شود.
 */
class TenancyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        //
    }
}
