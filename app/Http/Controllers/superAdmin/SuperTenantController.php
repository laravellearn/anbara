<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SuperAdmin\StoreTenantRequest;
use App\Http\Requests\SuperAdmin\UpdateTenantRequest;
use App\Models\ActivityLog;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SuperTenantController extends Controller
{
    public function index(Request $request)
    {
        $query = Tenant::withCount('users')
            ->with('activeSubscription.plan')
            ->latest();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('name', 'like', "%{$s}%")
                ->orWhere('email', 'like', "%{$s}%")
                ->orWhere('slug',  'like', "%{$s}%"));
        }
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }
        if ($request->filled('plan_id')) {
            $query->whereHas('activeSubscription', fn($q) => $q->where('plan_id', $request->plan_id));
        }

        $tenants = $query->paginate(20)->withQueryString();
        $plans   = Plan::where('is_active', true)->orderBy('sort_order')->get();

        $stats = [
            'total'    => Tenant::count(),
            'active'   => Tenant::where('is_active', true)->count(),
            'inactive' => Tenant::where('is_active', false)->count(),
            'expiring' => Subscription::where('status', 'active')
                ->whereNotNull('ends_at')
                ->where('ends_at', '<=', now()->addDays(30))
                ->count(),
        ];

        return view('super-admin.tenants.index', compact('tenants', 'plans', 'stats'));
    }

    public function show(Tenant $tenant)
    {
        $tenant->loadCount('users')
               ->load(['users' => fn($q) => $q->orderBy('name')->take(20)]);

        $subscriptions = Subscription::with('plan')
            ->where('tenant_id', $tenant->id)
            ->orderByDesc('created_at')
            ->get();

        $logs = ActivityLog::with('user')
            ->where('tenant_id', $tenant->id)
            ->latest()
            ->take(20)
            ->get();

        $plans = Plan::where('is_active', true)->orderBy('sort_order')->get();

        $stats = [
            'users_count'   => $tenant->users_count,
            'active_sub'    => $subscriptions->where('status', 'active')->first(),
            'total_subs'    => $subscriptions->count(),
        ];

        return view('super-admin.tenants.show', compact('tenant', 'subscriptions', 'logs', 'plans', 'stats'));
    }

    public function create()
    {
        $plans = Plan::where('is_active', true)->orderBy('sort_order')->get();
        return view('super-admin.tenants.create', compact('plans'));
    }

    public function store(StoreTenantRequest $request)
    {
        $data = $request->validated();
        $data['data'] = json_encode([]);
        $tenant = Tenant::create($data);

        // تخصیص پلن اولیه در صورت انتخاب
        if ($request->filled('plan_id')) {
            $plan = Plan::find($request->plan_id);
            Subscription::create([
                'tenant_id' => $tenant->id,
                'plan_id'   => $plan->id,
                'starts_at' => now(),
                'ends_at'   => $plan->duration_days ? now()->addDays($plan->duration_days) : null,
                'status'    => 'active',
                'auto_renew'=> false,
            ]);
        }

        return redirect()->route('super-admin.tenants.index')
            ->with('success', 'سازمان با موفقیت ایجاد شد.');
    }

    public function edit(Tenant $tenant)
    {
        $plans = Plan::where('is_active', true)->orderBy('sort_order')->get();
        return view('super-admin.tenants.edit', compact('tenant', 'plans'));
    }

    public function update(UpdateTenantRequest $request, Tenant $tenant)
    {
        $tenant->update($request->validated());
        return redirect()->route('super-admin.tenants.show', $tenant)
            ->with('success', 'اطلاعات سازمان به‌روزرسانی شد.');
    }

    public function destroy(Tenant $tenant)
    {
        $tenant->delete();
        return redirect()->route('super-admin.tenants.index')
            ->with('success', 'سازمان غیرفعال شد.');
    }

    // ─── تغییر وضعیت فعال/غیرفعال ────────────────────────────────────────────
    public function toggleStatus(Tenant $tenant)
    {
        $tenant->update(['is_active' => !$tenant->is_active]);
        $label = $tenant->is_active ? 'فعال' : 'غیرفعال';
        return back()->with('success', "سازمان {$label} شد.");
    }

    // ─── تخصیص پلن به سازمان ─────────────────────────────────────────────────
    public function assignPlan(Request $request, Tenant $tenant)
    {
        $request->validate([
            'plan_id'    => 'required|exists:plans,id',
            'starts_at'  => 'required|date',
        ]);

        $plan = Plan::findOrFail($request->plan_id);

        // لغو اشتراک فعلی
        Subscription::where('tenant_id', $tenant->id)
            ->where('status', 'active')
            ->update(['status' => 'canceled']);

        Subscription::create([
            'tenant_id'  => $tenant->id,
            'plan_id'    => $plan->id,
            'starts_at'  => $request->starts_at,
            'ends_at'    => $plan->duration_days
                ? \Carbon\Carbon::parse($request->starts_at)->addDays($plan->duration_days)
                : null,
            'status'     => 'active',
            'auto_renew' => false,
        ]);

        return back()->with('success', "پلن «{$plan->name}» به سازمان تخصیص یافت.");
    }
}
