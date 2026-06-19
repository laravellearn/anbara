<?php

namespace App\Concerns;

use App\Models\ActivityLog;
use App\Services\SubscriptionService;

trait ChecksSubscriptionLimit
{
    protected function checkLimit(string $featureKey, $model): void
    {
        $tenant = $model->tenant;
        if (!$tenant) return;

        $service = app(SubscriptionService::class);
        if (!$service->canCreate($featureKey, $tenant)) {
            ActivityLog::create([
                'tenant_id'   => $tenant->id,
                'user_id'     => auth()->id(),
                'action'      => 'subscription_limit_reached',
                'subject_type'=> get_class($model),
                'subject_id'  => 0,
                'description' => "محدودیت {$featureKey} به پایان رسیده است.",
            ]);
            throw new \Exception("ظرفیت {$featureKey} تکمیل شده است.");
        }
    }

    protected function incrementUsage(string $featureKey, $model): void
    {
        $tenant = $model->tenant;
        if (!$tenant) return;

        app(SubscriptionService::class)->incrementUsage($tenant, $featureKey);
    }
}