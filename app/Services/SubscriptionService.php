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
        // اشتراک‌های «فعال» و «آزمایشی» هر دو مجاز به استفاده از امکانات هستند
        return $tenant->subscriptions()
                      ->whereIn('status', ['active', 'trial'])
                      ->where('starts_at', '<=', now())
                      ->where(function ($q) {
                          // فعال: بدون تاریخ پایان یا هنوز منقضی نشده
                          $q->where(function ($active) {
                              $active->where('status', 'active')
                                     ->where(function ($d) {
                                         $d->whereNull('ends_at')
                                           ->orWhere('ends_at', '>=', now());
                                     });
                          })
                          // آزمایشی: trial_ends_at هنوز نگذشته
                          ->orWhere(function ($trial) {
                              $trial->where('status', 'trial')
                                    ->where('trial_ends_at', '>=', now());
                          });
                      })
                      ->orderByDesc('id')
                      ->first();
    }
}