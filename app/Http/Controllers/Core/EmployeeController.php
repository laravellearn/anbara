<?php

namespace App\Http\Controllers\Core;

use App\Models\Employee;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class EmployeeController extends BaseController
{
    public function index(Request $request)
    {
        Gate::authorize('access', 'employees.view');
        $tenantId = $this->manager->getTenantId();

        $units = Unit::where('tenant_id', $tenantId)->get();

        $query = Employee::with('unit')->where('tenant_id', $tenantId);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('employee_code', 'like', "%{$search}%")
                    ->orWhere('mobile', 'like', "%{$search}%");
            });
        }
        if ($request->filled('unit_id')) {
            $query->where('unit_id', $request->unit_id);
        }
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $query->latest();
        $employees = $query->paginate($request->per_page ?? 20);

        $stats = [
            'total'  => Employee::where('tenant_id', $tenantId)->count(),
            'active' => Employee::where('tenant_id', $tenantId)->where('is_active', true)->count(),
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

        $data = $request->validate([
            'unit_id'         => 'nullable|exists:units,id',
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

        flash()->success('کارمند ایجاد شد.');
        return redirect()->route('core.employees.index');
    }

    public function update(Request $request, Employee $employee)
    {
        Gate::authorize('access', 'employees.edit');

        $employee->update($request->validate([
            'unit_id'         => 'nullable|exists:units,id',
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
        ]));

        flash()->success('کارمند ویرایش شد.');
        return redirect()->route('core.employees.index');
    }

    public function destroy(Employee $employee)
    {
        Gate::authorize('access', 'employees.delete');

        $employee->delete();

        flash()->success('کارمند حذف شد.');
        return back();
    }
}
