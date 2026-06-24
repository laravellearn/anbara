<?php

namespace App\Http\Controllers\Core;

use App\Models\Employee;
use App\Models\OrganizationalUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class EmployeeController extends BaseController
{
    public function index(Request $request)
    {
        Gate::authorize('access', 'employees.view');
        $tenantId = $this->manager->getTenantId();

        // استفاده از OrganizationalUnit به‌جای Unit
        $units = OrganizationalUnit::where('tenant_id', $tenantId)->get();

        $query = Employee::with('unit')->where('tenant_id', $tenantId);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('employee_code', 'like', "%{$search}%")
                    ->orWhere('mobile', 'like', "%{$search}%");
            });
        }
        if ($request->filled('organizational_unit_id')) {
            $query->where('organizational_unit_id', $request->organizational_unit_id);
        }
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $query->latest();
        $employees = $query->paginate($request->per_page ?? 20);

        $stats = [
            'total'    => Employee::where('tenant_id', $tenantId)->count(),
            'active'   => Employee::where('tenant_id', $tenantId)->where('is_active', true)->count(),
            'inactive' => Employee::where('tenant_id', $tenantId)->where('is_active', false)->count(),
        ];

        if ($request->ajax() || $request->input('ajax')) {
            return response()->json([
                'html'      => view('core.employees._table', compact('employees'))->render(),
                'statsHtml' => view('core.employees._stats', compact('stats'))->render(),
                'total'     => $employees->total(),
            ]);
        }

        return view('core.employees.index', compact('employees', 'units', 'stats'));
    }

    public function store(Request $request)
    {
        Gate::authorize('access', 'employees.create');

        try {
            $data = $request->validate([
                'organizational_unit_id' => 'nullable|exists:organizational_units,id',
                'user_id'         => 'nullable|exists:users,id',
                'employee_code'   => 'nullable|string|max:50',
                'name'            => 'required|string|max:255',
                'national_code'   => 'nullable|string|max:20',
                'mobile'          => 'nullable|string|max:20',
                'phone'           => 'nullable|string|max:20',
                'position'        => 'nullable|string|max:255',
                'employment_date' => 'nullable|date',
                'address'         => 'nullable|string',
                'description'     => 'nullable|string',
                'is_active'       => 'boolean',
            ]);

            $data['tenant_id']  = $this->manager->getTenantId();
            $data['company_id'] = $this->manager->getCompanyId();

            Employee::create($data);

            return redirect()->route('core.employees.index')->with('toast', [
                'message' => 'کارمند با موفقیت ایجاد شد.',
                'type'    => 'success',
                'title'   => 'ایجاد کارمند'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('show_create_modal', true);
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'خطا در ایجاد کارمند: ' . $e->getMessage()])
                ->withInput()
                ->with('show_create_modal', true);
        }
    }

    public function update(Request $request, Employee $employee)
    {
        Gate::authorize('access', 'employees.edit');

        try {
            $data = $request->validate([
                'organizational_unit_id' => 'nullable|exists:organizational_units,id',
                'user_id'         => 'nullable|exists:users,id',
                'employee_code'   => 'nullable|string|max:50',
                'name'            => 'required|string|max:255',
                'national_code'   => 'nullable|string|max:20',
                'mobile'          => 'nullable|string|max:20',
                'phone'           => 'nullable|string|max:20',
                'position'        => 'nullable|string|max:255',
                'employment_date' => 'nullable|date',
                'address'         => 'nullable|string',
                'description'     => 'nullable|string',
                'is_active'       => 'boolean',
            ]);

            $employee->update($data);

            return redirect()->route('core.employees.index')->with('toast', [
                'message' => 'کارمند با موفقیت ویرایش شد.',
                'type'    => 'success',
                'title'   => 'ویرایش کارمند'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('show_edit_modal', true);
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'خطا در ویرایش کارمند: ' . $e->getMessage()])
                ->withInput()
                ->with('show_edit_modal', true);
        }
    }

    public function destroy(Employee $employee)
    {
        Gate::authorize('access', 'employees.delete');

        try {
            $employee->delete();

            return redirect()->route('core.employees.index')->with('toast', [
                'message' => 'کارمند با موفقیت حذف شد.',
                'type'    => 'success',
                'title'   => 'حذف کارمند'
            ]);
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'خطا در حذف کارمند: ' . $e->getMessage()]);
        }
    }
}