<?php

namespace App\Http\Controllers\SuperAdmin;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;

class SuperActivityLogController extends Controller
{
    public function index()
    {
        $logs = ActivityLog::with('user', 'tenant')->latest()->paginate(20);
        return view('super-admin.activity-logs.index', compact('logs'));
    }
}