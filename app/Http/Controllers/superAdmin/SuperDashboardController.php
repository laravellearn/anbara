<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class SuperDashboardController extends Controller
{
    public function index()
    {
        // ─── آمار پایه ───────────────────────────────────────────────────────
        $totalTenants      = Tenant::count();
        $activeTenants     = Tenant::where('is_active', true)->count();
        $inactiveTenants   = $totalTenants - $activeTenants;
        $totalUsers        = User::whereNotNull('tenant_id')->count();
        $totalSubscriptions= Subscription::where('status', 'active')->count();

        // ─── اشتراک‌های در حال انقضا (۳۰ روز آینده) ─────────────────────────
        $expiringSoon = Subscription::with(['tenant', 'plan'])
            ->where('status', 'active')
            ->whereNotNull('ends_at')
            ->whereBetween('ends_at', [now(), now()->addDays(30)])
            ->orderBy('ends_at')
            ->get();

        // ─── درآمد ───────────────────────────────────────────────────────────
        $monthlyRevenue = Subscription::join('plans', 'plans.id', '=', 'subscriptions.plan_id')
            ->where('subscriptions.status', 'active')
            ->sum('plans.monthly_price');

        // ─── رشد: سازمان‌های جدید ماه جاری ──────────────────────────────────
        $newTenantsThisMonth = Tenant::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // ─── توزیع پلن‌ها ─────────────────────────────────────────────────────
        $planDistribution = Subscription::join('plans', 'plans.id', '=', 'subscriptions.plan_id')
            ->where('subscriptions.status', 'active')
            ->groupBy('plans.id', 'plans.name')
            ->select('plans.name', DB::raw('COUNT(*) as cnt'))
            ->orderByDesc('cnt')
            ->get();

        // ─── ۵ سازمان اخیر ────────────────────────────────────────────────────
        $recentTenants = Tenant::with('activeSubscription.plan')
            ->latest()
            ->take(5)
            ->get();

        // ─── ۱۰ فعالیت اخیر ──────────────────────────────────────────────────
        $recentLogs = ActivityLog::with(['user', 'tenant'])
            ->latest()
            ->take(10)
            ->get();

        return view('super-admin.dashboard', compact(
            'totalTenants', 'activeTenants', 'inactiveTenants',
            'totalUsers', 'totalSubscriptions',
            'expiringSoon', 'monthlyRevenue',
            'newTenantsThisMonth', 'planDistribution',
            'recentTenants', 'recentLogs'
        ));
    }
}
