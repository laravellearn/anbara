<?php

namespace App\Http\Controllers\Warehouse;

use App\Models\WarehouseLocation;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class WarehouseLocationController extends BaseController
{
    public function index()
    {
        Gate::authorize('access', 'warehouse-locations.view');

        $locations = WarehouseLocation::with('warehouse')
            ->where('tenant_id', $this->manager->getTenantId())
            ->latest()
            ->paginate(20);

        $warehouses = Warehouse::where('tenant_id', $this->manager->getTenantId())->get();

        return view('warehouse.warehouse-locations.index', compact('locations', 'warehouses'));
    }

    public function create()
    {
        Gate::authorize('access', 'warehouse-locations.create');

        $warehouses = Warehouse::where('tenant_id', $this->manager->getTenantId())->get();
        $locations  = WarehouseLocation::where('tenant_id', $this->manager->getTenantId())->get();

        return view('warehouse.warehouse-locations.create', compact('warehouses', 'locations'));
    }

    public function store(Request $request)
    {
        Gate::authorize('access', 'warehouse-locations.create');

        $data = $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'parent_id'    => 'nullable|exists:warehouse_locations,id',
            'code'         => 'required|string|max:50',
            'title'        => 'required|string|max:255',
            'description'  => 'nullable|string',
            'sort_order'   => 'nullable|integer|min:0',
            'is_active'    => 'boolean',
        ]);

        $data['tenant_id']  = $this->manager->getTenantId();
        $data['company_id'] = $this->manager->getCompanyId();

        WarehouseLocation::create($data);

        flash()->success('موقعیت انبار ایجاد شد.');
        return redirect()->route('warehouse.warehouse-locations.index');
    }

    public function edit(WarehouseLocation $warehouseLocation)
    {
        Gate::authorize('access', 'warehouse-locations.edit');

        $warehouses = Warehouse::where('tenant_id', $this->manager->getTenantId())->get();
        $locations  = WarehouseLocation::where('tenant_id', $this->manager->getTenantId())
                        ->where('id', '!=', $warehouseLocation->id)
                        ->get();

        return view('warehouse.warehouse-locations.edit', compact('warehouseLocation', 'warehouses', 'locations'));
    }

    public function update(Request $request, WarehouseLocation $warehouseLocation)
    {
        Gate::authorize('access', 'warehouse-locations.edit');

        $data = $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'parent_id'    => 'nullable|exists:warehouse_locations,id',
            'code'         => 'required|string|max:50',
            'title'        => 'required|string|max:255',
            'description'  => 'nullable|string',
            'sort_order'   => 'nullable|integer|min:0',
            'is_active'    => 'boolean',
        ]);

        $warehouseLocation->update($data);

        flash()->success('موقعیت انبار ویرایش شد.');
        return redirect()->route('warehouse.warehouse-locations.index');
    }

    public function destroy(WarehouseLocation $warehouseLocation)
    {
        Gate::authorize('access', 'warehouse-locations.delete');

        $warehouseLocation->delete();

        flash()->success('موقعیت انبار حذف شد.');
        return back();
    }
}