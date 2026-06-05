<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Services\TenantManager;
use Illuminate\Support\Facades\Gate;

class ActivityLogController extends Controller
{
    public function index(TenantManager $manager)
    {
        Gate::authorize('access', 'activity_logs.view');

        $query = ActivityLog::with('user');

        if (!auth()->user()->isSuperAdmin()) {
            // فقط لاگ‌های Tenant جاری
            $query->where('tenant_id', $manager->getTenantId());
        }

        $logs = $query->latest()->paginate(20);

        return view('core.activity-logs.index', compact('logs'));
    }
}