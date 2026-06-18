<?php

namespace App\Http\Controllers\Core;

use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class OrganizationalUnitController extends BaseController
{
    public function index()
    {
        Gate::authorize('access', 'organizational-units.view');

        $units = Unit::with('parent')
            ->where('tenant_id', $this->manager->getTenantId())
            ->latest()
            ->paginate(20);

        $allUnits = Unit::where('tenant_id', $this->manager->getTenantId())->get();

        return view('core.organizational-units.index', compact('units', 'allUnits'));
    }

    public function store(Request $request)
    {
        Gate::authorize('access', 'organizational-units.create');

        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'parent_id'   => 'nullable|exists:units,id',
            'description' => 'nullable|string',
            'is_active'   => 'boolean',
        ]);

        $data['tenant_id']  = $this->manager->getTenantId();
        $data['company_id'] = $this->manager->getCompanyId();

        Unit::create($data);

        flash()->success('واحد سازمانی ایجاد شد.');
        return redirect()->route('core.organizational-units.index');
    }

    public function update(Request $request, Unit $unit)
    {
        Gate::authorize('access', 'organizational-units.edit');

        $unit->update($request->validate([
            'title'       => 'required|string|max:255',
            'parent_id'   => 'nullable|exists:units,id',
            'description' => 'nullable|string',
            'is_active'   => 'boolean',
        ]));

        flash()->success('واحد سازمانی ویرایش شد.');
        return redirect()->route('core.organizational-units.index');
    }

    public function destroy(Unit $unit)
    {
        Gate::authorize('access', 'organizational-units.delete');

        $unit->delete();

        flash()->success('واحد سازمانی حذف شد.');
        return back();
    }
}