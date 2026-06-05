<?php

namespace App\Jobs;

use App\Models\Subscription;
use App\Models\Tenant;
use App\Models\Plan;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class HandleExpiredTrials implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        // دریافت اشتراک‌های trial که expired نشده‌اند ولی زمانشان گذشته
        $expiredSubscriptions = Subscription::where('status', 'active')
            ->whereHas('plan', fn($q) => $q->where('code', 'trial'))
            ->where('ends_at', '<', now())
            ->get();

        foreach ($expiredSubscriptions as $sub) {
            DB::transaction(function () use ($sub) {
                // ۱. غیرفعال کردن اشتراک trial
                $sub->update(['status' => 'expired']);

                $tenant = $sub->tenant;
                $freePlan = Plan::where('code', 'free')->first();
                if (! $freePlan) return;

                // ۲. ایجاد اشتراک free جدید
                $newSub = Subscription::create([
                    'tenant_id'  => $tenant->id,
                    'plan_id'    => $freePlan->id,
                    'starts_at'  => now(),
                    'ends_at'    => null,            // نامحدود
                    'status'     => 'active',
                    'auto_renew' => false,
                ]);

                // ۳. کپی یا ریست usages
                foreach ($freePlan->features as $feature) {
                    $newSub->usages()->create([
                        'feature_key' => $feature->feature_key,
                        'used_value'  => 0,
                    ]);
                }
            });
        }
    }
}