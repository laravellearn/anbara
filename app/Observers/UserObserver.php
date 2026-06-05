<?php

namespace App\Observers;

use App\Models\User;
use App\Services\SubscriptionService;
use App\Services\TenantManager;

class UserObserver
{
    public function creating(User $user)
    {
        $manager = app(TenantManager::class);

        // اگر Tenant Context وجود ندارد (مثلاً در مرحلهٔ ثبت‌نام اولیه)، کاری نکن
        if (! $manager->getTenant()) {
            return;
        }

        $tenant = $manager->requireTenant();
        $subscriptionService = app(SubscriptionService::class);

        if (! $subscriptionService->canCreate('max_users', $tenant)) {
            throw new \Exception('ظرفیت کاربران به پایان رسیده است.');
        }
    }

    public function created(User $user)
    {
        $manager = app(TenantManager::class);

        if (! $manager->getTenant()) {
            return;
        }

        $tenant = $manager->requireTenant();
        app(SubscriptionService::class)->incrementUsage($tenant, 'max_users');
    }
}