<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Services\TenantManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ActivityLogController extends Controller
{
    public function index(Request $request, TenantManager $manager)
    {
        Gate::authorize('access', 'activity_logs.view');

        $query = ActivityLog::with('user');

        if (!auth()->user()->isSuperAdmin()) {
            $query->where('tenant_id', $manager->getTenantId());
        }

        if ($request->filled('user')) {
            $query->whereHas('user', fn($q) => $q->where('name', 'like', '%'.$request->user.'%'));
        }
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }
        if ($request->filled('subject_type')) {
            $query->where('subject_type', 'like', '%'.$request->subject_type.'%');
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        $logs = $query->latest()->paginate(30)->withQueryString();

        return view('core.activity-logs.index', compact('logs'));
    }
}