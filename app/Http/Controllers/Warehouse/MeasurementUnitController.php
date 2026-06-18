<?php

namespace App\Http\Controllers\Warehouse;

use App\Models\MeasurementUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class MeasurementUnitController extends BaseController
{
    public function index()
    {
        Gate::authorize('access', 'measurement-units.view');

        $units = MeasurementUnit::with('parent')
            ->where('tenant_id', $this->manager->getTenantId())
            ->latest()
            ->paginate(20);

        $allUnits = MeasurementUnit::where('tenant_id', $this->manager->getTenantId())->get();

        return view('warehouse.measurement-units.index', compact('units', 'allUnits'));
    }

    public function store(Request $request)
    {
        Gate::authorize('access', 'measurement-units.create');

        $data = $request->validate([
            'title'             => 'required|string|max:255',
            'symbol'            => 'nullable|string|max:20',
            'parent_id'         => 'nullable|exists:measurement_units,id',
            'conversion_factor' => 'nullable|numeric|min:0',
            'description'       => 'nullable|string',
            'is_active'         => 'boolean',
        ]);

        $data['tenant_id'] = $this->manager->getTenantId();

        MeasurementUnit::create($data);

        flash()->success('واحد اندازه‌گیری ایجاد شد.');
        return redirect()->route('warehouse.measurement-units.index');
    }

    public function update(Request $request, MeasurementUnit $measurementUnit)
    {
        Gate::authorize('access', 'measurement-units.edit');

        $measurementUnit->update($request->validate([
            'title'             => 'required|string|max:255',
            'symbol'            => 'nullable|string|max:20',
            'parent_id'         => 'nullable|exists:measurement_units,id',
            'conversion_factor' => 'nullable|numeric|min:0',
            'description'       => 'nullable|string',
            'is_active'         => 'boolean',
        ]));

        flash()->success('واحد اندازه‌گیری ویرایش شد.');
        return redirect()->route('warehouse.measurement-units.index');
    }

    public function destroy(MeasurementUnit $measurementUnit)
    {
        Gate::authorize('access', 'measurement-units.delete');

        $measurementUnit->delete();

        flash()->success('واحد اندازه‌گیری حذف شد.');
        return back();
    }
}