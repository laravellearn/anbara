<?php

namespace App\Http\Controllers\SuperAdmin;
use App\Http\Controllers\Controller;
use App\Models\Subscription;
use Illuminate\Http\Request;

class SuperSubscriptionController extends Controller
{
    public function index()
    {
        $subscriptions = Subscription::with(['tenant', 'plan'])->latest()->paginate(20);
        return view('super-admin.subscriptions.index', compact('subscriptions'));
    }

    public function cancel(Subscription $subscription)
    {
        $subscription->update(['status' => 'canceled']);
        return back()->with('success', 'اشتراک لغو شد.');
    }

    public function renew(Request $request, Subscription $subscription)
    {
        // تمدید: یک اشتراک جدید با همان پلن می‌سازیم
        $newSub = Subscription::create([
            'tenant_id' => $subscription->tenant_id,
            'plan_id'   => $subscription->plan_id,
            'starts_at' => now(),
            'ends_at'   => $subscription->plan->duration_days ? now()->addDays($subscription->plan->duration_days) : null,
            'status'    => 'active',
            'auto_renew'=> false,
        ]);
        return back()->with('success', 'اشتراک تمدید شد.');
    }
}