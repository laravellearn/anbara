<?php

namespace App\Http\Controllers\Core;

use App\Models\OrganizationalUnit;
use App\Models\User;
use App\Http\Requests\Core\StoreOrganizationalUnitRequest;
use App\Http\Requests\Core\UpdateOrganizationalUnitRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class OrganizationalUnitController extends BaseController
{
    public function index(Request $request)
    {
        Gate::authorize('access', 'organizational-units.view');
        $tenantId = $this->manager->getTenantId();

        $query = OrganizationalUnit::with('parent')->where('tenant_id', $tenantId);
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
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

        $allUnits = OrganizationalUnit::where('tenant_id', $tenantId)->get();
        $users = User::where('tenant_id', $tenantId)->get(); // برای انتخاب مدیر

        return view('core.organizational-units.index', compact('units', 'stats', 'allUnits', 'users'));
    }

    public function store(StoreOrganizationalUnitRequest $request)
    {
        Gate::authorize('access', 'organizational-units.create');

        try {
            $tenantId = $this->manager->getTenantId();
            $data = $request->validated();

            $data['tenant_id'] = $tenantId;
            $data['company_id'] = $this->manager->getCompanyId();

            OrganizationalUnit::create($data);

            return redirect()->route('organizational-units.index')->with('toast', [
                'message' => 'واحد سازمانی با موفقیت ایجاد شد.',
                'type'    => 'success',
                'title'   => 'ایجاد واحد'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('show_create_modal', true);
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'خطا در ایجاد واحد: ' . $e->getMessage()])
                ->withInput()
                ->with('show_create_modal', true);
        }
    }

    public function update(UpdateOrganizationalUnitRequest $request, OrganizationalUnit $organizationalUnit)
    {
        Gate::authorize('access', 'organizational-units.edit');

        try {
            $data = $request->validated();

            $data['is_active'] = $request->boolean('is_active', false);
            $organizationalUnit->update($data);

            return redirect()->route('organizational-units.index')->with('toast', [
                'message' => 'واحد سازمانی با موفقیت ویرایش شد.',
                'type'    => 'success',
                'title'   => 'ویرایش واحد'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('show_edit_modal', true);
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'خطا در ویرایش واحد: ' . $e->getMessage()])
                ->withInput()
                ->with('show_edit_modal', true);
        }
    }

    public function destroy(OrganizationalUnit $organizationalUnit)
    {
        Gate::authorize('access', 'organizational-units.delete');

        try {
            $organizationalUnit->delete();

            return redirect()->route('organizational-units.index')->with('toast', [
                'message' => 'واحد سازمانی با موفقیت حذف شد.',
                'type'    => 'success',
                'title'   => 'حذف واحد'
            ]);
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'خطا در حذف واحد: ' . $e->getMessage()]);
        }
    }
}