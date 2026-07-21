<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;

class SuperAdminRoleController extends Controller
{
    public function index()
    {
        $roles = Role::withCount('users')->orderBy('name')->get();
        return view('super-admin.roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::orderBy('group')->orderBy('name')->get()->groupBy('group');
        return view('super-admin.roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:100|unique:roles,name',
            'title'       => 'required|string|max:200',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role = Role::create([
            'name'  => $request->name,
            'title' => $request->title,
        ]);

        if ($request->filled('permissions')) {
            $role->permissions()->sync($request->permissions);
        }

        return redirect()->route('super-admin.roles.index')
            ->with('success', 'نقش با موفقیت ایجاد شد.');
    }

    public function edit(Role $role)
    {
        $permissions      = Permission::orderBy('group')->orderBy('name')->get()->groupBy('group');
        $rolePermissions  = $role->permissions->pluck('id')->toArray();
        return view('super-admin.roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name'          => 'required|string|max:100|unique:roles,name,' . $role->id,
            'title'         => 'required|string|max:200',
            'permissions'   => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role->update([
            'name'  => $request->name,
            'title' => $request->title,
        ]);

        $role->permissions()->sync($request->permissions ?? []);

        return redirect()->route('super-admin.roles.index')
            ->with('success', 'نقش به‌روزرسانی شد.');
    }

    public function destroy(Role $role)
    {
        if ($role->users()->count() > 0) {
            return back()->withErrors(['error' => 'این نقش به کاربران اختصاص یافته و قابل حذف نیست.']);
        }
        $role->delete();
        return back()->with('success', 'نقش حذف شد.');
    }
}

