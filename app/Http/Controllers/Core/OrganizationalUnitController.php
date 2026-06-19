<?php

namespace App\Http\Controllers\Core;

use App\Models\OrganizationalUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class OrganizationalUnitController extends BaseController
{
    public function index(Request $request)
    {
        Gate::authorize('access', 'organizational-units.view');
        $tenantId = $this->manager->getTenantId();

        $allUnits = OrganizationalUnit::where('tenant_id', $tenantId)->get();

        $query = OrganizationalUnit::with('parent')->where('tenant_id', $tenantId);

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
            'total'    => OrganizationalUnit::where('tenant_id', $tenantId)->count(),
            'active'   => OrganizationalUnit::where('tenant_id', $tenantId)->where('is_active', true)->count(),
            'inactive' => OrganizationalUnit::where('tenant_id', $tenantId)->where('is_active', false)->count(),
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

        try {
            $data = $request->validate([
                'title'       => 'required|string|max:255',
                'parent_id'   => 'nullable|exists:units,id',
                'description' => 'nullable|string',
                'is_active'   => 'boolean',
            ]);

            $data['tenant_id']  = $this->manager->getTenantId();
            $data['company_id'] = $this->manager->getCompanyId();

            OrganizationalUnit::create($data);

            return redirect()->route('core.organizational-units.index')->with('toast', [
                'message' => 'واحد سازمانی با موفقیت ایجاد شد.',
                'type'    => 'success',
                'title'   => 'ایجاد واحد سازمانی'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('show_create_modal', true);
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'خطا در ایجاد واحد سازمانی: ' . $e->getMessage()])
                ->withInput()
                ->with('show_create_modal', true);
        }
    }

    public function update(Request $request, OrganizationalUnit $unit)
    {
        Gate::authorize('access', 'organizational-units.edit');

        try {
            $request->validate([
                'title'       => 'required|string|max:255',
                'parent_id'   => 'nullable|exists:units,id',
                'description' => 'nullable|string',
                'is_active'   => 'boolean',
            ]);

            $unit->update($request->only('title', 'parent_id', 'description', 'is_active'));

            return redirect()->route('core.organizational-units.index')->with('toast', [
                'message' => 'واحد سازمانی با موفقیت ویرایش شد.',
                'type'    => 'success',
                'title'   => 'ویرایش واحد سازمانی'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('show_edit_modal', true);
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'خطا در ویرایش واحد سازمانی: ' . $e->getMessage()])
                ->withInput()
                ->with('show_edit_modal', true);
        }
    }

    public function destroy(OrganizationalUnit $unit)
    {
        Gate::authorize('access', 'organizational-units.delete');

        try {
            $unit->delete();

            return redirect()->route('core.organizational-units.index')->with('toast', [
                'message' => 'واحد سازمانی با موفقیت حذف شد.',
                'type'    => 'success',
                'title'   => 'حذف واحد سازمانی'
            ]);
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'خطا در حذف واحد سازمانی: ' . $e->getMessage()]);
        }
    }
}