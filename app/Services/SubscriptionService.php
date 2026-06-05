<?php

namespace App\Services;

use App\Models\Tenant;
use App\Models\Subscription;
use App\Models\Plan;

class SubscriptionService
{
    /**
     * چک می‌کنه که tenant می‌تونه یک resource جدید (مثل کاربر) بسازه یا نه.
     */
    public function canCreate(string $featureKey, Tenant $tenant): bool
    {
        $subscription = $this->getActiveSubscription($tenant);
        if (!$subscription) return false;
        
        $limit = $this->getFeatureLimit($subscription->plan, $featureKey);
        if ($limit === null) return true; // محدودیت ندارد
        
        $used = $subscription->usages()->where('feature_key', $featureKey)->first();
        $current = $used ? $used->used_value : 0;
        
        return $current < $limit;
    }

    /**
     * بعد از ایجاد موفق، مقدار مصرف را افزایش بده.
     */
    public function incrementUsage(Tenant $tenant, string $featureKey): void
    {
        $subscription = $this->getActiveSubscription($tenant);
        if (!$subscription) return;
        
        $usage = $subscription->usages()->firstOrNew(['feature_key' => $featureKey]);
        $usage->used_value = ($usage->used_value ?? 0) + 1;
        $usage->save();
    }

    protected function getActiveSubscription(Tenant $tenant): ?Subscription
    {
        return $tenant->subscriptions()
                      ->where('status', 'active')
                      ->where('starts_at', '<=', now())
                      ->where('ends_at', '>=', now())
                      ->first();
    }

    protected function getFeatureLimit(Plan $plan, string $featureKey): ?int
    {
        $feature = $plan->features()->where('feature_key', $featureKey)->first();
        if (!$feature) return null;
        
        $value = json_decode($feature->feature_value, true);
        return $value['limit'] ?? null; // یا هر ساختاری که داری
    }
}