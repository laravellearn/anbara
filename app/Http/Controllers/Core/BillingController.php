<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Subscription;
use App\Services\PlanService;
use App\Services\TenantManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class BillingController extends Controller
{
    public function plans(PlanService $planService)
    {
        $currentPlan = $planService->getCurrentPlan();
        $allPlans = Plan::where('is_active', true)->orderBy('sort_order')->get();
        $upgradable = $planService->getUpgradablePlans();
        // آرایه‌ای از ID های قابل ارتقا برای نشان دادن دکمه مناسب
        $upgradableIds = array_map(fn($p) => $p->id, $upgradable);

        return view('core.billing.plans', compact('allPlans', 'currentPlan', 'upgradableIds'));
    }

    public function subscribe(Request $request, PlanService $planService)
    {
        $request->validate(['plan_id' => 'required|exists:plans,id']);
        $targetPlan = Plan::findOrFail($request->plan_id);

        $currentPlan = $planService->getCurrentPlan();
        if ($currentPlan && !$planService->canUpgradeTo($currentPlan, $targetPlan)) {
            return back()->withErrors(['plan' => 'امکان ارتقا به این پلن وجود ندارد.']);
        }

        // اینجا می‌توان فرآیند پرداخت را فراخوانی کرد.
        // برای نمونه، اشتراک جدید می‌سازیم (بدون پرداخت)

        // غیرفعال‌سازی اشتراک فعلی
        if ($currentPlan) {
            $activeSubscription = $planService->getActiveSubscription();
            if ($activeSubscription) {
                $activeSubscription->update(['status' => 'canceled']);
            }
        }

        $tenant = app(\App\Services\TenantManager::class)->requireTenant();
        \App\Models\Subscription::create([
            'tenant_id'  => $tenant->id,
            'plan_id'    => $targetPlan->id,
            'starts_at'  => now(),
            'ends_at'    => $targetPlan->duration_days ? now()->addDays($targetPlan->duration_days) : null,
            'status'     => 'active',
            'auto_renew' => false,
        ]);

        return redirect()->route('billing.license')->with('success', 'اشتراک شما با موفقیت به ' . $targetPlan->name . ' ارتقا یافت.');
    }

    public function license(PlanService $planService)
    {
        $subscription = $planService->getActiveSubscription();
        if (!$subscription) {
            return redirect()->route('billing.plans')->withErrors(['license' => 'اشتراک فعالی وجود ندارد.']);
        }

        $plan = $subscription->plan;
        $usageDetails = $planService->getCurrentUsageDetails();

        return view('core.billing.license', compact('subscription', 'plan', 'usageDetails'));
    }

    public function history(TenantManager $manager)
    {
        Gate::authorize('access', 'subscriptions.history');

        if (auth()->user()->isSuperAdmin()) {
            $subscriptions = Subscription::with(['tenant', 'plan'])->latest()->paginate(20);
        } else {
            $tenantId = $manager->getTenantId();
            $subscriptions = Subscription::with('plan')
                ->where('tenant_id', $tenantId)
                ->latest()
                ->paginate(20);
        }

        return view('core.billing.history', compact('subscriptions'));
    }
}
