<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Permission;
use App\Services\TenantManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

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

        // محاسبه آمار
        $stats = [
            'total'    => Role::where('tenant_id', $this->manager->getTenantId())->count(),
            'active'   => Role::where('tenant_id', $this->manager->getTenantId())->where('is_active', true)->count(),
            'inactive' => Role::where('tenant_id', $this->manager->getTenantId())->where('is_active', false)->count(),
        ];

        return view('core.roles.index', compact('roles','stats'));
    }

    public function store(Request $request)
    {
        Gate::authorize('access', 'roles.create');

        try {
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

            return redirect()->route('roles.index')->with('toast', [
                'message' => 'نقش جدید با موفقیت ایجاد شد.',
                'type' => 'success',
                'title' => 'ایجاد نقش'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'خطا در ایجاد نقش: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function update(Request $request, $id)
    {
        Gate::authorize('access', 'roles.edit');

        try {
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

            return redirect()->route('roles.index')->with('toast', [
                'message' => 'نقش با موفقیت ویرایش شد.',
                'type' => 'success',
                'title' => 'ویرایش نقش'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'خطا در ویرایش نقش: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function destroy($id)
    {
        Gate::authorize('access', 'roles.delete');

        try {
            $role = Role::where('tenant_id', $this->manager->getTenantId())->findOrFail($id);
            $role->delete();

            return redirect()->route('roles.index')->with('toast', [
                'message' => 'نقش با موفقیت حذف شد.',
                'type' => 'success',
                'title' => 'حذف نقش'
            ]);
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'خطا در حذف نقش: ' . $e->getMessage()]);
        }
    }

    public function create()
    {
        Gate::authorize('access', 'roles.create');
        $permissions = Permission::all()->groupBy('group');
        return view('core.roles.create', compact('permissions'));
    }

    public function edit(Role $role)
    {
        Gate::authorize('access', 'roles.edit');
        $permissions = Permission::all()->groupBy('group');
        $rolePermissions = $role->permissions->pluck('id')->toArray();
        return view('core.roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }
}
