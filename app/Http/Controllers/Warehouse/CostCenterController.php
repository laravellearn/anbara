<?php

namespace App\Http\Controllers\Warehouse;

use App\Models\CostCenter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CostCenterController extends BaseController
{
    public function index(Request $request)
    {
        Gate::authorize('access', 'cost-centers.view');

        $tenantId = $this->manager->getTenantId();

        $query = CostCenter::where('tenant_id', $tenantId);

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
        $costCenters = $query->paginate($request->per_page ?? 20);

        $stats = [
            'total'    => CostCenter::where('tenant_id', $tenantId)->count(),
            'active'   => CostCenter::where('tenant_id', $tenantId)->where('is_active', true)->count(),
            'inactive' => CostCenter::where('tenant_id', $tenantId)->where('is_active', false)->count(),
        ];

        if ($request->ajax() || $request->input('ajax')) {
            return response()->json([
                'html'      => view('warehouse.cost-centers._table', compact('costCenters'))->render(),
                'statsHtml' => view('warehouse.cost-centers._stats', compact('stats'))->render(),
                'total'     => $costCenters->total(),
            ]);
        }

        return view('warehouse.cost-centers.index', compact('costCenters', 'stats'));
    }

    public function store(Request $request)
    {
        Gate::authorize('access', 'cost-centers.create');

        $data = $request->validate([
            'code'        => 'required|string|max:50|unique:cost_centers,code',
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active'   => 'boolean',
        ]);

        $data['tenant_id']  = $this->manager->getTenantId();
        $data['company_id'] = $this->manager->getCompanyId();

        CostCenter::create($data);

        flash()->success('مرکز هزینه ایجاد شد.');
        return redirect()->route('warehouse.cost-centers.index');
    }

    public function update(Request $request, CostCenter $costCenter)
    {
        Gate::authorize('access', 'cost-centers.edit');

        $costCenter->update($request->validate([
            'code'        => 'required|string|max:50|unique:cost_centers,code,' . $costCenter->id,
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active'   => 'boolean',
        ]));

        flash()->success('مرکز هزینه ویرایش شد.');
        return redirect()->route('warehouse.cost-centers.index');
    }

    public function destroy(CostCenter $costCenter)
    {
        Gate::authorize('access', 'cost-centers.delete');

        $costCenter->delete();

        flash()->success('مرکز هزینه حذف شد.');
        return back();
    }
}