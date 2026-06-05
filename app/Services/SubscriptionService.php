<?php

namespace App\Services;

use App\Models\Tenant;
use App\Models\Subscription;
use App\Models\Plan;

class SubscriptionService
{
    public function canCreate(string $featureKey, Tenant $tenant): bool
    {
        $subscription = $this->getActiveSubscription($tenant);
        if (! $subscription) return false;

        $limit = $subscription->plan->getLimit($featureKey);
        if ($limit === null) return true; // نامحدود

        $used = $subscription->usages()->where('feature_key', $featureKey)->first();
        $current = $used ? $used->used_value : 0;

        return $current < $limit;
    }

    public function incrementUsage(Tenant $tenant, string $featureKey): void
    {
        $subscription = $this->getActiveSubscription($tenant);
        if (! $subscription) return;

        $usage = $subscription->usages()->firstOrNew(['feature_key' => $featureKey]);
        $usage->used_value = ($usage->used_value ?? 0) + 1;
        $usage->save();
    }

    protected function getActiveSubscription(Tenant $tenant): ?Subscription
    {
        return $tenant->subscriptions()
                      ->where('status', 'active')
                      ->where('starts_at', '<=', now())
                      ->where(function ($q) {
                          $q->whereNull('ends_at')->orWhere('ends_at', '>=', now());
                      })
                      ->first();
    }
}