<?php

namespace App\Http\Controllers\Warehouse;

use App\Models\MeasurementUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class MeasurementUnitController extends BaseController
{
    public function index(Request $request)
    {
        Gate::authorize('access', 'measurement-units.view');
        $tenantId = $this->manager->getTenantId();
        $allUnits = MeasurementUnit::where('tenant_id', $tenantId)->get();

        $query = MeasurementUnit::with('parent')->where('tenant_id', $tenantId);
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                    ->orWhere('symbol', 'like', "%{$request->search}%");
            });
        }
        if ($request->filled('parent_id')) {
            $query->where('parent_id', $request->parent_id);
        }
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }
        $query->latest();
        $units = $query->paginate($request->per_page ?? 20);

        $stats = [
            'total'    => MeasurementUnit::where('tenant_id', $tenantId)->count(),
            'active'   => MeasurementUnit::where('tenant_id', $tenantId)->where('is_active', true)->count(),
            'inactive' => MeasurementUnit::where('tenant_id', $tenantId)->where('is_active', false)->count(),
        ];

        if ($request->ajax() || $request->input('ajax')) {
            return response()->json([
                'html'      => view('warehouse.measurement-units._table', compact('units'))->render(),
                'statsHtml' => view('warehouse.measurement-units._stats', compact('stats'))->render(),
                'total'     => $units->total(),
            ]);
        }

        return view('warehouse.measurement-units.index', compact('units', 'allUnits', 'stats'));
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
