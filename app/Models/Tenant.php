<?php

namespace App\Models;

use App\Concerns\Auditable;
use App\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Tenant extends Model
{
    use SoftDeletes,Auditable,LogsActivity;

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
        'is_active',
        'trial_ends_at',
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
        'settings'      => 'array',
        'is_active'     => 'boolean',
        'trial_ends_at' => 'datetime',
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


    public function roles()
    {
        return $this->hasMany(Role::class);
    }


    // app/Models/Tenant.php
    public function fiscalYears()
    {
        return $this->hasMany(FiscalYear::class);
    }

    /**
     * سال مالی فعال جاری (بر اساس is_active = true)
     */
    public function activeFiscalYear()
    {
        return $this->hasOne(FiscalYear::class)->where('is_active', true);
    }

    // app/Models/Tenant.php
    public function rootCompany()
    {
        return $this->hasOne(Company::class)->whereNull('parent_id');
    }

    /**
     * رابطهٔ یک به چند با اشتراک‌ها
     */
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * آخرین اشتراک فعال یا آزمایشی (رابطه)
     * فقط اشتراک‌هایی که هنوز منقضی نشده‌اند برگردانده می‌شوند.
     */
    public function activeSubscription(): HasOne
    {
        return $this->hasOne(Subscription::class)
            ->where(function ($q) {
                // اشتراک فعال: بدون تاریخ پایان یا هنوز معتبر
                $q->where(function ($active) {
                    $active->where('status', 'active')
                           ->where(function ($d) {
                               $d->whereNull('ends_at')
                                 ->orWhere('ends_at', '>=', now());
                           });
                })
                // اشتراک آزمایشی: trial_ends_at هنوز نگذشته
                ->orWhere(function ($trial) {
                    $trial->where('status', 'trial')
                          ->where('trial_ends_at', '>=', now());
                });
            })
            ->orderByDesc('id');
    }

    /**
     * بررسی وجود اشتراک فعال
     */
    public function hasActiveSubscription(): bool
    {
        // فراخوانی رابطه به‌صورت property (بدون پرانتز)
        $subscription = $this->activeSubscription;

        if (!$subscription) {
            return false;
        }

        if ($subscription->status === 'active') {
            return $subscription->ends_at === null || now()->lt($subscription->ends_at);
        }

        if ($subscription->status === 'trial') {
            return $subscription->trial_ends_at !== null && now()->lt($subscription->trial_ends_at);
        }

        return false;
    }
}
