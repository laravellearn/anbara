<?php

namespace App\Concerns;

use App\Scopes\TenantScope;

trait BelongsToTenant
{
    protected static function bootBelongsToTenant(): void
    {
        static::addGlobalScope(new TenantScope());
    }
    
    // می‌تونی متدهایی برای bypass کردن اسکوپ هم اضافه کنی
    public static function withoutTenantScope()
    {
        return static::withoutGlobalScope(TenantScope::class);
    }
}