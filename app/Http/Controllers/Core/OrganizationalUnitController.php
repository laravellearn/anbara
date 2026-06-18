<?php

namespace App\Http\Controllers\Core;

use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class OrganizationalUnitController extends BaseController
{
    public function index(Request $request)
    {
        Gate::authorize('access', 'organizational-units.view');
        $tenantId = $this->manager->getTenantId();

        $allUnits = Unit::where('tenant_id', $tenantId)->get();

        $query = Unit::with('parent')->where('tenant_id', $tenantId);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                    ->orWhere('description', 'like', "%{$request->search}%");
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
            'total'    => Unit::where('tenant_id', $tenantId)->count(),
            'active'   => Unit::where('tenant_id', $tenantId)->where('is_active', true)->count(),
            'inactive' => Unit::where('tenant_id', $tenantId)->where('is_active', false)->count(),
        ];

        if ($request->ajax() || $request->input('ajax')) {
            return response()->json([
                'html'      => view('core.organizational-units._table', compact('units'))->render(),
                'statsHtml' => view('core.organizational-units._stats', compact('stats'))->render(),
                'total'     => $units->total(),
            ]);
        }

        return view('core.organizational-units.index', compact('units', 'allUnits', 'stats'));
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
