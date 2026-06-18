<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\WarehouseLocation;
use App\Models\Warehouse;
use App\Services\TenantManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class WarehouseLocationController extends Controller
{
    protected $manager;

    public function __construct(TenantManager $manager)
    {
        $this->manager = $manager;
    }

    public function index()
    {
        Gate::authorize('access', 'warehouse-locations.view');

        $locations = WarehouseLocation::where('tenant_id', $this->manager->getTenantId())
            ->with('warehouse')
            ->latest()
            ->paginate(20);

        $warehouses = Warehouse::where('tenant_id', $this->manager->getTenantId())->get();

        return view('warehouse.locations.index', compact('locations', 'warehouses'));
    }

    public function store(Request $request)
    {
        Gate::authorize('access', 'warehouse-locations.create');

        $data = $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'parent_id'    => 'nullable|exists:warehouse_locations,id',
            'code'         => 'required|string|max:50',
            'name'         => 'nullable|string|max:255',
            'type'         => 'required|in:aisle,rack,shelf,bin',
            'capacity'     => 'nullable|numeric|min:0',
            'is_active'    => 'boolean',
        ]);

        $data['tenant_id'] = $this->manager->getTenantId();
        WarehouseLocation::create($data);

        flash()->success('موقعیت جدید ایجاد شد.');
        return redirect()->route('warehouse.warehouse-locations.index');
    }

    public function update(Request $request, WarehouseLocation $location)
    {
        Gate::authorize('access', 'warehouse-locations.edit');

        $location->update($request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'parent_id'    => 'nullable|exists:warehouse_locations,id',
            'code'         => 'required|string|max:50',
            'name'         => 'nullable|string|max:255',
            'type'         => 'required|in:aisle,rack,shelf,bin',
            'capacity'     => 'nullable|numeric|min:0',
            'is_active'    => 'boolean',
        ]));

        flash()->success('موقعیت ویرایش شد.');
        return redirect()->route('warehouse.warehouse-locations.index');
    }

    public function destroy(WarehouseLocation $location)
    {
        Gate::authorize('access', 'warehouse-locations.delete');

        $location->delete();
        flash()->success('موقعیت حذف شد.');
        return back();
    }
}