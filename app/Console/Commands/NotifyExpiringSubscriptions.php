<?php

namespace App\Console\Commands;

use App\Mail\SubscriptionExpiringMail;
use App\Models\Subscription;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotifyExpiringSubscriptions extends Command
{
    protected $signature   = 'subscriptions:notify-expiring {--days=7 : تعداد روزهای باقیمانده برای هشدار}';
    protected $description = 'ارسال ایمیل هشدار برای اشتراک‌هایی که ظرف N روز آینده منقضی می‌شوند';

    public function handle(): int
    {
        $days = (int) $this->option('days');
        $now  = now();

        // اشتراک‌های فعال که ظرف $days روز منقضی می‌شوند
        $subscriptions = Subscription::with(['tenant', 'plan'])
            ->whereIn('status', ['active', 'trial'])
            ->where(function ($q) use ($now, $days) {
                // active → ends_at
                $q->where(function ($a) use ($now, $days) {
                    $a->where('status', 'active')
                      ->whereNotNull('ends_at')
                      ->whereBetween('ends_at', [$now, $now->copy()->addDays($days)]);
                })
                // trial → trial_ends_at
                ->orWhere(function ($t) use ($now, $days) {
                    $t->where('status', 'trial')
                      ->whereNotNull('trial_ends_at')
                      ->whereBetween('trial_ends_at', [$now, $now->copy()->addDays($days)]);
                });
            })
            ->get();

        $count = 0;
        foreach ($subscriptions as $sub) {
            $tenant = $sub->tenant;
            if (! $tenant || ! $tenant->email) {
                continue;
            }

            $endsAt      = $sub->trial_ends_at ?? $sub->ends_at;
            $remainDays  = (int) $now->diffInDays($endsAt, false);

            try {
                Mail::to($tenant->email)
                    ->send(new SubscriptionExpiringMail($tenant, $sub, $remainDays));
                $count++;
                $this->line("✔ ارسال شد → {$tenant->name} ({$tenant->email}) — {$remainDays} روز مانده");
            } catch (\Throwable $e) {
                Log::error("خطا در ارسال هشدار اشتراک به {$tenant->email}: " . $e->getMessage());
                $this->warn("✗ خطا → {$tenant->email}: " . $e->getMessage());
            }
        }

        $this->info("تعداد {$count} ایمیل هشدار ارسال شد.");
        return self::SUCCESS;
    }
}
