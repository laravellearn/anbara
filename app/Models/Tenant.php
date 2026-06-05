<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tenant extends Model
{
    use SoftDeletes;

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

    // app/Models/Tenant.php
    public function activeSubscription(): ?Subscription
    {
        return $this->subscriptions()
            ->where('status', 'active')
            ->where('starts_at', '<=', now())
            ->where(function ($q) {
                $q->whereNull('ends_at')
                    ->orWhere('ends_at', '>=', now());
            })
            ->orderByDesc('starts_at')
            ->first(); // → یک نمونه برمی‌گرداند، نه hasOne
    }
}
