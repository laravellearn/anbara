<?php

namespace App\Http\Controllers\Warehouse;

use App\Models\CostCenter;
use App\Http\Requests\Warehouse\StoreCostCenterRequest;
use App\Http\Requests\Warehouse\UpdateCostCenterRequest;
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

    public function store(StoreCostCenterRequest $request)
    {
        Gate::authorize('access', 'cost-centers.create');

        try {
            $data = $request->validated();
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
            \Log::error($e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            return redirect()->back()
                ->withErrors(['error' => 'خطا در ایجاد مرکز هزینه'])
                ->withInput()
                ->with('show_create_modal', true);
        }
    }

    public function update(UpdateCostCenterRequest $request, CostCenter $costCenter)
    {
        Gate::authorize('access', 'cost-centers.edit');

        try {
            $costCenter->update($request->validated());

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
            \Log::error($e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            return redirect()->back()
                ->withErrors(['error' => 'خطا در ویرایش مرکز هزینه'])
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
            \Log::error($e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            return redirect()->back()
                ->withErrors(['error' => 'خطا در حذف مرکز هزینه']);
        }
    }
}