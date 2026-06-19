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

        try {
            $data = $request->validate([
                'code'        => 'required|string|max:50|unique:cost_centers,code',
                'title'       => 'required|string|max:255',
                'description' => 'nullable|string',
                'is_active'   => 'boolean',
            ]);

            $data['tenant_id']  = $this->manager->getTenantId();
            $data['company_id'] = $this->manager->getCompanyId();

            CostCenter::create($data);

            return redirect()->route('warehouse.cost-centers.index')->with('toast', [
                'message' => 'مرکز هزینه با موفقیت ایجاد شد.',
                'type'    => 'success',
                'title'   => 'ایجاد مرکز هزینه'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('show_create_modal', true);
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'خطا در ایجاد مرکز هزینه: ' . $e->getMessage()])
                ->withInput()
                ->with('show_create_modal', true);
        }
    }

    public function update(Request $request, CostCenter $costCenter)
    {
        Gate::authorize('access', 'cost-centers.edit');

        try {
            $data = $request->validate([
                'code'        => 'required|string|max:50|unique:cost_centers,code,' . $costCenter->id,
                'title'       => 'required|string|max:255',
                'description' => 'nullable|string',
                'is_active'   => 'boolean',
            ]);

            $costCenter->update($data);

            return redirect()->route('warehouse.cost-centers.index')->with('toast', [
                'message' => 'مرکز هزینه با موفقیت ویرایش شد.',
                'type'    => 'success',
                'title'   => 'ویرایش مرکز هزینه'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('show_edit_modal', true);
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'خطا در ویرایش مرکز هزینه: ' . $e->getMessage()])
                ->withInput()
                ->with('show_edit_modal', true);
        }
    }

    public function destroy(CostCenter $costCenter)
    {
        Gate::authorize('access', 'cost-centers.delete');

        try {
            $costCenter->delete();

            return redirect()->route('warehouse.cost-centers.index')->with('toast', [
                'message' => 'مرکز هزینه با موفقیت حذف شد.',
                'type'    => 'success',
                'title'   => 'حذف مرکز هزینه'
            ]);
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'خطا در حذف مرکز هزینه: ' . $e->getMessage()]);
        }
    }
}