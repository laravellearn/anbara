<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains;

    protected $fillable = [
        'name',
        'title',
        'slug',
        'domain',
        'email',
        'phone',
        'address',
        'website',
        'logo_path',
        'favicon_path',
        'theme_color',
        'data',
        'settings',
        'is_active'
    ];

    public static function getCustomColumns(): array
    {
        return [
            'id',
            'name',
            'title',
            'slug',
            'domain',
            'email',
            'phone',
            'is_active'
        ];
    }

    protected $casts = [
        'settings' => 'array',
        'is_active' => 'boolean',
    ];

    // Business Layer
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function companies(): HasMany
    {
        return $this->hasMany(Company::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function roles()
    {
        return $this->hasMany(Role::class);
    }
}





