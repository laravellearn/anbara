<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use App\Services\TenantManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class WarehouseController extends Controller
{
    protected $manager;

    public function __construct(TenantManager $manager)
    {
        $this->manager = $manager;
    }

    public function index()
    {
        Gate::authorize('access', 'warehouses.view');

        $warehouses = Warehouse::where('tenant_id', $this->manager->getTenantId())
            ->with('company')
            ->latest()
            ->paginate(20);

        return view('warehouse.warehouses.index', compact('warehouses'));
    }

    public function store(Request $request)
    {
        Gate::authorize('access', 'warehouses.create');

        $data = $request->validate([
            'name'         => 'required|string|max:255',
            'code'         => 'nullable|string|max:50',
            'address'      => 'nullable|string',
            'manager_user_id'=> 'nullable|exists:users,id',
            'capacity'     => 'nullable|numeric|min:0',
            'is_active'    => 'boolean',
        ]);

        $data['tenant_id']  = $this->manager->getTenantId();
        $data['company_id'] = $this->manager->getCompanyId();

        Warehouse::create($data);

        flash()->success('انبار جدید ایجاد شد.');
        return redirect()->route('warehouse.warehouses.index');
    }

    public function update(Request $request, Warehouse $warehouse)
    {
        Gate::authorize('access', 'warehouses.edit');

        $warehouse->update($request->validate([
            'name'         => 'required|string|max:255',
            'code'         => 'nullable|string|max:50',
            'address'      => 'nullable|string',
            'manager_user_id'=> 'nullable|exists:users,id',
            'capacity'     => 'nullable|numeric|min:0',
            'is_active'    => 'boolean',
        ]));

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