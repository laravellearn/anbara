<?php

namespace App\Services;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Tenant;
use App\Services\TenantManager;

class PlanService
{
    protected $manager;

    public function __construct(TenantManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * اشتراک فعال Tenant را برمی‌گرداند.
     */
    public function getActiveSubscription(): ?Subscription
    {
        $tenant = $this->manager->getTenant();
        if (!$tenant) return null;

        // استفاده از property (بدون پرانتز) – مدل Subscription را برمی‌گرداند
        return $tenant->activeSubscription;
    }
    /**
     * پلن‌های قابل ارتقا را برمی‌گرداند.
     * (همهٔ پلن‌های فعال که از پلن فعلی بالاتر هستند)
     */
    public function getUpgradablePlans(): array
    {
        $currentPlan = $this->getCurrentPlan();
        $allPlans = Plan::where('is_active', true)->orderBy('sort_order')->get();

        if (! $currentPlan) return $allPlans->all();

        return $allPlans->filter(function ($plan) use ($currentPlan) {
            return $plan->id !== $currentPlan->id
                && $plan->monthly_price > $currentPlan->monthly_price;
        })->values()->all();
    }
    /**
     * پلن فعلی Tenant را برمی‌گرداند.
     */
    public function getCurrentPlan(): ?Plan
    {
        $subscription = $this->getActiveSubscription();
        return $subscription ? $subscription->plan : null;
    }

    /**
     * آیا می‌توان از $currentPlan به $targetPlan ارتقا داد؟
     */
    public function canUpgradeTo(Plan $currentPlan, Plan $targetPlan): bool
    {
        if ($currentPlan->id === $targetPlan->id || !$targetPlan->is_active) return false;
        if ($currentPlan->slug === 'enterprise') return false;

        // استفاده از monthly_price
        if ($currentPlan->monthly_price == 0 && $targetPlan->monthly_price > 0) return true;
        if ($currentPlan->monthly_price > 0 && $targetPlan->monthly_price > $currentPlan->monthly_price) return true;

        return false;
    }


    /**
     * تعداد و لیست محدودیت‌های پلن فعلی به‌همراه مصرف جاری.
     * برای صفحه لایسنس استفاده می‌شود.
     */
    public function getCurrentUsageDetails(): array
    {
        $subscription = $this->getActiveSubscription();
        if (!$subscription) return [];

        $plan   = $subscription->plan;
        $limits = $plan->limits ?? [];
        $usages = $subscription->usages()->pluck('used_value', 'feature_key')->all();

        $details = [];
        foreach ($limits as $key => $limit) {
            // فقط محدودیت‌های عددی را بررسی کن
            if (!is_numeric($limit)) continue;

            $limit = (int) $limit;          // تبدیل به عدد صحیح
            if ($limit <= 0) continue;      // محدودیت صفر یا منفی بی‌معنی است

            $used = (int) ($usages[$key] ?? 0);
            $percent = min(100, round(($used / $limit) * 100));

            $details[] = [
                'key'     => $key,
                'label'   => __("limits.{$key}"),
                'limit'   => $limit,
                'used'    => $used,
                'percent' => $percent,
            ];
        }

        return $details;
    }

    public function tenantHasFeature(string $featureKey): bool
    {
        $plan = $this->getCurrentPlan();
        return $plan ? $plan->hasFeature($featureKey) : false;
    }
}
