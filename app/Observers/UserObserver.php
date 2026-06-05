<?php

namespace App\Observers;

use App\Models\User;
use App\Services\SubscriptionService;
use App\Services\TenantManager;

class UserObserver
{
    public function creating(User $user)
    {
        $tenant = app(TenantManager::class)->requireTenant();
        $subscriptionService = app(SubscriptionService::class);

        if (! $subscriptionService->canCreate('users', $tenant)) {
            throw new \Exception('ظرفیت کاربران به پایان رسیده است.');
        }
    }

    public function created(User $user)
    {
        $tenant = app(TenantManager::class)->requireTenant();
        app(SubscriptionService::class)->incrementUsage($tenant, 'users');
    }
}