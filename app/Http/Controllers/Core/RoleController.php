<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Permission;
use App\Services\TenantManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class RoleController extends Controller
{
    protected $manager;

    public function __construct(TenantManager $manager)
    {
        $this->manager = $manager;
    }

    public function index()
    {
        Gate::authorize('access', 'roles.view');

        $roles = Role::where('tenant_id', $this->manager->getTenantId())
            ->with('permissions')
            ->latest()
            ->paginate(20);
        $groupedPermissions = Permission::getGroupedPermissions(); // اضافه کنید

        return view('core.roles.index', compact('roles', 'groupedPermissions'));
    }

    public function store(Request $request)
    {
        Gate::authorize('access', 'roles.create');

        $request->validate([
            'code' => 'required|string|unique:roles,code,NULL,id,tenant_id,' . $this->manager->getTenantId(),
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role = Role::create([
            'tenant_id' => $this->manager->getTenantId(),
            'code' => $request->code,
            'title' => $request->title,
            'description' => $request->description,
            'is_system' => false,
            'is_active' => true,
        ]);

        $role->permissions()->sync($request->permissions ?? []);

        return redirect()->route('roles.index')->with('swal_success', 'نقش جدید ایجاد شد.');
    }

    public function update(Request $request, $id)
    {
        Gate::authorize('access', 'roles.edit');

        $role = Role::where('tenant_id', $this->manager->getTenantId())->findOrFail($id);

        $request->validate([
            'code' => 'required|string|unique:roles,code,' . $role->id . ',id,tenant_id,' . $this->manager->getTenantId(),
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role->update($request->only('code', 'title', 'description'));
        $role->permissions()->sync($request->permissions ?? []);

        return redirect()->route('roles.index')->with('swal_success', 'نقش ویرایش شد.');
    }

    public function destroy($id)
    {
        Gate::authorize('access', 'roles.delete');

        $role = Role::where('tenant_id', $this->manager->getTenantId())->findOrFail($id);
        $role->delete();

        return redirect()->route('roles.index')->with('swal_success', 'نقش حذف شد.');
    }

    public function create()
    {
        Gate::authorize('access', 'roles.create');
        $permissions = \App\Models\Permission::all()->groupBy('group');
        return view('roles.create', compact('permissions'));
    }

    public function edit(Role $role)
    {
        Gate::authorize('access', 'roles.edit');
        $permissions = \App\Models\Permission::all()->groupBy('group');
        $rolePermissions = $role->permissions->pluck('id')->toArray();
        return view('roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }
}
