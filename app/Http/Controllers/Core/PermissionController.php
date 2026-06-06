<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Support\Facades\Gate;

class PermissionController extends Controller
{
    public function index()
    {
        Gate::authorize('access', 'permissions.view');

        $permissions = Permission::orderBy('name')->get();

        return view('core.permissions.index', compact('permissions'));
    }
}