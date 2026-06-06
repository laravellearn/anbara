<?php

namespace App\Http\Controllers\SuperAdmin;
use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\Subscription;

class SuperDashboardController extends Controller
{
    public function index()
    {
        $totalTenants = Tenant::count();
        $activeTenants = Tenant::where('is_active', true)->count();
        $totalSubscriptions = Subscription::where('status', 'active')->count();
        return view('super-admin.dashboard', compact('totalTenants', 'activeTenants', 'totalSubscriptions'));
    }
}