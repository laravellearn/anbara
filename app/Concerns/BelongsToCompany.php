<?php

namespace App\Concerns;

use App\Scopes\CompanyScope;

trait BelongsToCompany
{
    protected static function bootBelongsToCompany(): void
    {
        static::addGlobalScope(new CompanyScope());
    }

    public static function withoutCompanyScope()
    {
        return static::withoutGlobalScope(CompanyScope::class);
    }
}