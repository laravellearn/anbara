<?php

namespace App\Http\Controllers\Warehouse;

use App\Models\Warehouse;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class WarehouseController extends BaseController
{
    public function index(Request $request)
    {
        Gate::authorize('access', 'warehouses.view');
        $tenantId = $this->manager->getTenantId();

        $query = Warehouse::with('company')->where('tenant_id', $tenantId);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $query->latest();
        $warehouses = $query->paginate($request->per_page ?? 20);

        $stats = [
            'total'    => Warehouse::where('tenant_id', $tenantId)->count(),
            'active'   => Warehouse::where('tenant_id', $tenantId)->where('is_active', true)->count(),
            'inactive' => Warehouse::where('tenant_id', $tenantId)->where('is_active', false)->count(),
        ];

        if ($request->ajax() || $request->input('ajax')) {
            return response()->json([
                'html'      => view('warehouse.warehouses._table', compact('warehouses'))->render(),
                'statsHtml' => view('warehouse.warehouses._stats', compact('stats'))->render(),
                'total'     => $warehouses->total(),
            ]);
        }

        return view('warehouse.warehouses.index', compact('warehouses', 'stats'));
    }

    public function create()
    {
        Gate::authorize('access', 'warehouses.create');

        $users = User::where('tenant_id', $this->manager->getTenantId())->get();

        return view('warehouse.warehouses.create', compact('users'));
    }

    public function store(Request $request)
    {
        Gate::authorize('access', 'warehouses.create');

        $data = $request->validate([
            'code'                 => 'required|string|max:50|unique:warehouses,code',
            'title'                => 'required|string|max:255',
            'description'          => 'nullable|string',
            'address'              => 'nullable|string',
            'allow_negative_stock' => 'boolean',
            'is_active'            => 'boolean',
            'users'                => 'nullable|array',
            'users.*'              => 'exists:users,id',
        ]);

        $data['tenant_id']  = $this->manager->getTenantId();
        $data['company_id'] = $this->manager->getCompanyId();

        $warehouse = Warehouse::create($data);

        if ($request->has('users')) {
            $warehouse->users()->sync($request->users);
        }

        flash()->success('انبار ایجاد شد.');
        return redirect()->route('warehouse.warehouses.index');
    }

    public function edit(Warehouse $warehouse)
    {
        Gate::authorize('access', 'warehouses.edit');

        $users = User::where('tenant_id', $this->manager->getTenantId())->get();
        $warehouse->load('users');

        return view('warehouse.warehouses.edit', compact('warehouse', 'users'));
    }

    public function update(Request $request, Warehouse $warehouse)
    {
        Gate::authorize('access', 'warehouses.edit');

        $data = $request->validate([
            'code'                 => 'required|string|max:50|unique:warehouses,code,' . $warehouse->id,
            'title'                => 'required|string|max:255',
            'description'          => 'nullable|string',
            'address'              => 'nullable|string',
            'allow_negative_stock' => 'boolean',
            'is_active'            => 'boolean',
            'users'                => 'nullable|array',
            'users.*'              => 'exists:users,id',
        ]);

        $warehouse->update($data);

        if ($request->has('users')) {
            $warehouse->users()->sync($request->users);
        }

        flash()->success('انبار ویرایش شد.');
        return redirect()->route('warehouse.warehouses.index');
    }

    public function destroy(Warehouse $warehouse)
    {
        Gate::authorize('access', 'warehouses.delete');

        $warehouse->delete();

        flash()->success('انبار حذف شد.');
        return back();
    }
}
