<?php

namespace App\Http\Controllers\Warehouse;

use App\Models\WarehouseLocation;
use App\Models\Warehouse;
use App\Http\Requests\Warehouse\StoreWarehouseLocationRequest;
use App\Http\Requests\Warehouse\UpdateWarehouseLocationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class WarehouseLocationController extends BaseController
{
    public function index(Request $request)
    {
        Gate::authorize('access', 'warehouse-locations.view');
        $tenantId = $this->manager->getTenantId();

        $warehouses = Warehouse::where('tenant_id', $tenantId)->get();

        $query = WarehouseLocation::with('warehouse')->where('tenant_id', $tenantId);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%");
            });
        }
        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $query->latest();
        $locations = $query->paginate($request->per_page ?? 20);

        $stats = [
            'total'    => WarehouseLocation::where('tenant_id', $tenantId)->count(),
            'active'   => WarehouseLocation::where('tenant_id', $tenantId)->where('is_active', true)->count(),
            'inactive' => WarehouseLocation::where('tenant_id', $tenantId)->where('is_active', false)->count(),
        ];

        if ($request->ajax() || $request->input('ajax')) {
            return response()->json([
                'html'      => view('warehouse.warehouse-locations._table', compact('locations'))->render(),
                'statsHtml' => view('warehouse.warehouse-locations._stats', compact('stats'))->render(),
                'total'     => $locations->total(),
            ]);
        }

        return view('warehouse.warehouse-locations.index', compact('locations', 'warehouses', 'stats'));
    }

    public function create()
    {
        Gate::authorize('access', 'warehouse-locations.create');

        $warehouses = Warehouse::where('tenant_id', $this->manager->getTenantId())->get();
        $locations  = WarehouseLocation::where('tenant_id', $this->manager->getTenantId())->get();

        return view('warehouse.warehouse-locations.create', [
            'warehouses' => $warehouses,
            'locations'  => $locations,
            'location'   => null,  // اضافه شد
        ]);
    }

    public function store(StoreWarehouseLocationRequest $request)
    {
        Gate::authorize('access', 'warehouse-locations.create');

        try {
            $data = $request->validated();

            $data['tenant_id']  = $this->manager->getTenantId();
            $data['company_id'] = $this->manager->getCompanyId();
            $data['is_active']  = $request->boolean('is_active', false); // تبدیل قطعی

            WarehouseLocation::create($data);

            return redirect()->route('warehouse.warehouse-locations.index')->with('toast', [
                'message' => 'موقعیت انبار با موفقیت ایجاد شد.',
                'type'    => 'success',
                'title'   => 'ایجاد موقعیت'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            \Log::error($e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            return redirect()->back()
                ->withErrors(['error' => 'خطا در ایجاد موقعیت'])
                ->withInput();
        }
    }

    public function edit(WarehouseLocation $warehouseLocation)
    {
        Gate::authorize('access', 'warehouse-locations.edit');

        $warehouses = Warehouse::where('tenant_id', $this->manager->getTenantId())->get();
        $locations  = WarehouseLocation::where('tenant_id', $this->manager->getTenantId())
            ->where('id', '!=', $warehouseLocation->id)
            ->get();

        // تغییر نام متغیر برای هماهنگی با ویو
        return view('warehouse.warehouse-locations.edit', [
            'location'  => $warehouseLocation,
            'warehouses' => $warehouses,
            'locations' => $locations,
        ]);
    }

    public function update(UpdateWarehouseLocationRequest $request, WarehouseLocation $warehouseLocation)
    {
        Gate::authorize('access', 'warehouse-locations.edit');

        try {
            $data = $request->validated();

            $data['is_active'] = $request->boolean('is_active', false);
            $warehouseLocation->update($data);

            return redirect()->route('warehouse.warehouse-locations.index')->with('toast', [
                'message' => 'موقعیت انبار با موفقیت ویرایش شد.',
                'type'    => 'success',
                'title'   => 'ویرایش موقعیت'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            \Log::error($e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            return redirect()->back()
                ->withErrors(['error' => 'خطا در ویرایش موقعیت'])
                ->withInput();
        }
    }

    public function destroy(WarehouseLocation $warehouseLocation)
    {
        Gate::authorize('access', 'warehouse-locations.delete');

        try {
            $warehouseLocation->delete();

            return redirect()->route('warehouse.warehouse-locations.index')->with('toast', [
                'message' => 'موقعیت انبار با موفقیت حذف شد.',
                'type'    => 'success',
                'title'   => 'حذف موقعیت'
            ]);
        } catch (\Exception $e) {
            \Log::error($e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            return redirect()->back()
                ->withErrors(['error' => 'خطا در حذف موقعیت']);
        }
    }
}
