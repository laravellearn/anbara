<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Subscription;
use App\Services\TenantManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class BillingController extends Controller
{
    public function __construct(protected TenantManager $manager) {}

    // ─── نمایش پلن‌ها ──────────────────────────────────────────────────────
    public function plans()
    {
        $tenant          = $this->manager->getTenant();
        $currentSub      = $tenant?->activeSubscription;
        $currentPlan     = $currentSub?->plan;
        $allPlans        = Plan::where('is_active', true)->orderBy('sort_order')->get();
        $expiresInDays   = null;

        if ($currentSub) {
            $endsAt = $currentSub->trial_ends_at ?? $currentSub->ends_at;
            if ($endsAt) {
                $expiresInDays = max(0, (int) now()->diffInDays($endsAt, false));
            }
        }

        return view('core.billing.plans', compact('allPlans', 'currentPlan', 'currentSub', 'expiresInDays'));
    }

    // ─── انتخاب پلن ─────────────────────────────────────────────────────────
    public function subscribe(Request $request)
    {
        $request->validate(['plan_id' => 'required|exists:plans,id']);

        $tenant = $this->manager->getTenant();
        $plan   = Plan::findOrFail($request->plan_id);

        // لغو اشتراک فعلی
        Subscription::where('tenant_id', $tenant->id)
            ->whereIn('status', ['active', 'trial'])
            ->update(['status' => 'cancelled']);

        // ایجاد اشتراک جدید
        Subscription::create([
            'tenant_id' => $tenant->id,
            'plan_id'   => $plan->id,
            'starts_at' => now(),
            'ends_at'   => $plan->duration_days ? now()->addDays($plan->duration_days) : null,
            'status'    => 'active',
            'auto_renew'=> false,
        ]);

        return redirect()->route('billing.license')
            ->with('success', "اشتراک «{$plan->name}» با موفقیت فعال شد.");
    }

    // ─── وضعیت لایسنس ──────────────────────────────────────────────────────
    public function license()
    {
        Gate::authorize('access', 'license.view');

        $tenant       = $this->manager->getTenant();
        $subscription = $tenant?->activeSubscription?->load('plan');
        $plan         = $subscription?->plan;
        $usages       = $subscription?->usages ?? collect();

        $expiresInDays = null;
        if ($subscription) {
            $endsAt = $subscription->trial_ends_at ?? $subscription->ends_at;
            if ($endsAt) {
                $expiresInDays = max(0, (int) now()->diffInDays($endsAt, false));
            }
        }

        return view('core.billing.license', compact('subscription', 'plan', 'usages', 'expiresInDays'));
    }

    // ─── تاریخچه اشتراک‌ها ──────────────────────────────────────────────────
    public function history()
    {
        Gate::authorize('access', 'subscriptions.history');

        $tenant        = $this->manager->getTenant();
        $subscriptions = Subscription::with('plan')
            ->where('tenant_id', $tenant->id)
            ->orderByDesc('id')
            ->paginate(15);

        return view('core.billing.history', compact('subscriptions'));
    }
}
