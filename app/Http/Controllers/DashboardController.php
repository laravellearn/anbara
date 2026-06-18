<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use App\Services\TenantManager;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        /** @var TenantManager $manager */
        $manager = app(TenantManager::class);
        $companyId = $manager->getCompanyId();

        if (auth()->user()?->isSuperAdmin()) {
            return redirect()->route('super-admin.dashboard');
        }

        $tenant = $manager->getTenant();

        $activeSubscription = $tenant ? $tenant->subscriptions()->with('plan')->where('status', 'active')->where('starts_at', '<=', now())->where(function ($q) {
            $q->whereNull('ends_at')->orWhere('ends_at', '>=', now());
        })->first() : null;

        $data = [
            'productsCount'    => Product::where('tenant_id', $tenant->id)->count(),
            'warehousesCount'  => Warehouse::where('tenant_id', $tenant->id)->count(),
            'usersCount'       => User::where('tenant_id', $tenant->id)->count(),
            'categoriesCount'  => Category::where('tenant_id', $tenant->id)->count(),
            'activeSubscription' => $activeSubscription,
            'recentActivities' => ActivityLog::where('tenant_id', $tenant->id)->latest()->take(10)->get(),
            'user' => $user,
            'companyId' => $companyId
        ];

        return view('dashboard', $data);
    }
}
