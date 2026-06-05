<?php

namespace App\Concerns;

use App\Scopes\TenantScope;

/**
 * @mixin \Illuminate\Database\Eloquent\Model
 */
trait BelongsToTenant
{
    protected static function bootBelongsToTenant(): void
    {
        static::addGlobalScope(new TenantScope());
    }

    public static function withoutTenantScope()
    {
        return static::withoutGlobalScope(TenantScope::class);
    }
}