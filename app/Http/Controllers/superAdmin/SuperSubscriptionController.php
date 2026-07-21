<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Tenant;
use Illuminate\Http\Request;

class SuperSubscriptionController extends Controller
{
    public function index(Request $request)
    {
        $query = Subscription::with(['tenant', 'plan'])->latest();

        if ($request->filled('tenant_id')) {
            $query->where('tenant_id', $request->tenant_id);
        }
        if ($request->filled('plan_id')) {
            $query->where('plan_id', $request->plan_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('expiring')) {
            $query->whereNotNull('ends_at')->where('ends_at', '<=', now()->addDays(30));
        }

        $subscriptions = $query->paginate(25)->withQueryString();
        $tenants = Tenant::orderBy('name')->get(['id', 'name']);
        $plans   = Plan::orderBy('sort_order')->get(['id', 'name']);

        $stats = [
            'active'   => Subscription::where('status', 'active')->count(),
            'expiring' => Subscription::where('status', 'active')
                ->whereNotNull('ends_at')
                ->where('ends_at', '<=', now()->addDays(30))->count(),
            'canceled' => Subscription::where('status', 'canceled')->count(),
        ];

        return view('super-admin.subscriptions.index', compact('subscriptions', 'tenants', 'plans', 'stats'));
    }

    public function create()
    {
        $tenants = Tenant::where('is_active', true)->orderBy('name')->get(['id', 'name']);
        $plans   = Plan::where('is_active', true)->orderBy('sort_order')->get();
        return view('super-admin.subscriptions.create', compact('tenants', 'plans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tenant_id'  => 'required|exists:tenants,id',
            'plan_id'    => 'required|exists:plans,id',
            'starts_at'  => 'required|date',
            'cancel_old' => 'boolean',
        ]);

        $plan = Plan::findOrFail($request->plan_id);

        if ($request->boolean('cancel_old')) {
            Subscription::where('tenant_id', $request->tenant_id)
                ->where('status', 'active')
                ->update(['status' => 'canceled']);
        }

        Subscription::create([
            'tenant_id'  => $request->tenant_id,
            'plan_id'    => $plan->id,
            'starts_at'  => $request->starts_at,
            'ends_at'    => $plan->duration_days
                ? \Carbon\Carbon::parse($request->starts_at)->addDays($plan->duration_days)
                : null,
            'status'     => 'active',
            'auto_renew' => false,
        ]);

        return redirect()->route('super-admin.subscriptions.index')
            ->with('success', 'اشتراک جدید با موفقیت ثبت شد.');
    }

    public function cancel(Subscription $subscription)
    {
        $subscription->update(['status' => 'canceled']);
        return back()->with('success', 'اشتراک لغو شد.');
    }

    public function renew(Request $request, Subscription $subscription)
    {
        $plan = $subscription->plan;
        Subscription::create([
            'tenant_id'  => $subscription->tenant_id,
            'plan_id'    => $subscription->plan_id,
            'starts_at'  => now(),
            'ends_at'    => $plan->duration_days ? now()->addDays($plan->duration_days) : null,
            'status'     => 'active',
            'auto_renew' => false,
        ]);
        $subscription->update(['status' => 'expired']);
        return back()->with('success', 'اشتراک تمدید شد.');
    }
}
