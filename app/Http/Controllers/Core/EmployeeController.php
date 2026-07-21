<?php

namespace App\Http\Controllers\Core;

use App\Models\CompanyUser;
use App\Models\Employee;
use App\Models\Contact;
use App\Models\OrganizationalUnit;
use App\Models\Role;
use App\Models\User;
use App\Http\Requests\Core\StoreEmployeeRequest;
use App\Http\Requests\Core\UpdateEmployeeRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class EmployeeController extends BaseController
{
    public function index(Request $request)
    {
        Gate::authorize('access', 'employees.view');
        $tenantId = $this->manager->getTenantId();

        $units = OrganizationalUnit::where('tenant_id', $tenantId)->get();
        $users = User::where('tenant_id', $tenantId)->get();

        $query = Employee::with(['organizationalUnit', 'user', 'contact'])
            ->where('tenant_id', $tenantId);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('employee_code', 'like', "%{$search}%")
                    ->orWhere('mobile', 'like', "%{$search}%")
                    ->orWhereHas('contact', function ($sub) use ($search) {
                        $sub->where('email', 'like', "%{$search}%");
                    });
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

        $currentCompanyId = $this->manager->getCompanyId();

        // نقش‌های قابل انتخاب برای کاربر جدید: عمومی + اختصاصی شرکت جاری
        $roles = Role::where('tenant_id', $tenantId)
            ->where(function ($q) use ($currentCompanyId) {
                $q->whereNull('company_id')
                    ->orWhere('company_id', $currentCompanyId);
            })
            ->get();

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

        return view('core.employees.index', compact('employees', 'units', 'users', 'stats', 'roles'));
    }

public function store(StoreEmployeeRequest $request)
{
    Gate::authorize('access', 'employees.create');

    try {
        $tenantId  = $this->manager->getTenantId();
        $companyId = $this->manager->getCompanyId();

        $validated = $request->validated();

        // ۱. ساخت Contact از نوع کارمند
        $contactData = [
            'tenant_id'     => $tenantId,
            'company_id'    => $companyId,
            'type'          => \App\Enums\ContactType::EMPLOYEE->value,
            'first_name'    => $validated['name'],
            'mobile'        => $validated['mobile'] ?? null,
            'phone'         => $validated['phone'] ?? null,
            'email'         => $validated['email'] ?? null,
            'national_code' => $validated['national_code'] ?? null,
            'address'       => $validated['address'] ?? null,
            'description'   => $validated['description'] ?? null,
            'is_active'     => $validated['is_active'] ?? true,
        ];
        $contact = Contact::create($contactData);

        // ۲. ایجاد کاربر در صورت درخواست
        $userId = null;
        if ($request->boolean('create_user')) {
            // ساخت کاربر
            $user = User::create([
                'tenant_id' => $tenantId,
                'name'      => $validated['name'],
                'username'  => $validated['username'],
                'password'  => bcrypt($validated['password']),
                'mobile'    => $validated['mobile'] ?? null,
                'is_active' => true,
                'contact_id'=> $contact->id,   // اتصال کاربر به همین مخاطب
            ]);

            // اتصال کاربر به شرکت جاری
            $user->companies()->attach($companyId, [
                'tenant_id'  => $tenantId,
                'is_default' => true,
            ]);

            // اختصاص نقش انتخاب‌شده در همین شرکت
            $companyUser = CompanyUser::where('user_id', $user->id)
                ->where('company_id', $companyId)
                ->first();
            if ($companyUser) {
                $companyUser->roles()->sync([$validated['role_id']]);
            }

            $userId = $user->id;
        }

        // ۳. ذخیره کارمند
        $employeeData = $validated;
        $employeeData['tenant_id']  = $tenantId;
        $employeeData['company_id'] = $companyId;
        $employeeData['contact_id'] = $contact->id;
        $employeeData['user_id']    = $userId;

        Employee::create($employeeData);

        return redirect()->route('employees.index')->with('toast', [
            'message' => 'کارمند با موفقیت ایجاد شد.',
            'type'    => 'success',
            'title'   => 'ایجاد کارمند'
        ]);
    } catch (\Illuminate\Validation\ValidationException $e) {
        return redirect()->back()->withErrors($e->errors())->withInput()->with('show_create_modal', true);
    } catch (\Exception $e) {
        \Log::error($e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
        return redirect()->back()->withErrors(['error' => 'خطا در ایجاد کارمند'])->withInput()->with('show_create_modal', true);
    }
}

public function update(UpdateEmployeeRequest $request, Employee $employee)
{
    Gate::authorize('access', 'employees.edit');

    try {
        $tenantId  = $this->manager->getTenantId();
        $companyId = $this->manager->getCompanyId();

        $validated = $request->validated();

        // بروزرسانی Contact
        $contactData = [
            'tenant_id'     => $tenantId,
            'company_id'    => $companyId,
            'type'          => \App\Enums\ContactType::EMPLOYEE->value,
            'first_name'    => $validated['name'],
            'mobile'        => $validated['mobile'] ?? null,
            'phone'         => $validated['phone'] ?? null,
            'email'         => $validated['email'] ?? null,
            'national_code' => $validated['national_code'] ?? null,
            'address'       => $validated['address'] ?? null,
            'description'   => $validated['description'] ?? null,
            'is_active'     => $validated['is_active'] ?? true,
        ];

        if ($employee->contact_id) {
            $contact = Contact::find($employee->contact_id);
            if ($contact) {
                $contact->update($contactData);
            } else {
                $contact = Contact::create($contactData);
                $employee->contact_id = $contact->id;
            }
        } else {
            $contact = Contact::create($contactData);
            $employee->contact_id = $contact->id;
        }

        // اگر درخواست ایجاد کاربر جدید داده شده و کارمند قبلاً کاربر ندارد
        if ($request->boolean('create_user') && !$employee->user_id) {
            $user = User::create([
                'tenant_id' => $tenantId,
                'name'      => $validated['name'],
                'username'  => $validated['username'],
                'password'  => bcrypt($validated['password']),
                'mobile'    => $validated['mobile'] ?? null,
                'is_active' => true,
                'contact_id'=> $employee->contact_id,
            ]);

            $user->companies()->attach($companyId, [
                'tenant_id'  => $tenantId,
                'is_default' => true,
            ]);

            $companyUser = CompanyUser::where('user_id', $user->id)
                ->where('company_id', $companyId)
                ->first();
            if ($companyUser) {
                $companyUser->roles()->sync([$validated['role_id']]);
            }

            $validated['user_id'] = $user->id;
        } else {
            // نگه‌داشتن user_id قبلی (در صورت وجود)
            $validated['user_id'] = $employee->user_id;
        }

        $employeeData = $validated;
        $employeeData['contact_id'] = $employee->contact_id;
        $employeeData['is_active']  = $request->boolean('is_active', false);

        $employee->update($employeeData);

        return redirect()->route('core.employees.index')->with('toast', [
            'message' => 'کارمند با موفقیت ویرایش شد.',
            'type'    => 'success',
            'title'   => 'ویرایش کارمند'
        ]);
    } catch (\Illuminate\Validation\ValidationException $e) {
        return redirect()->back()->withErrors($e->errors())->withInput()->with('show_edit_modal', true);
    } catch (\Exception $e) {
        \Log::error($e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
        return redirect()->back()->withErrors(['error' => 'خطا در ویرایش کارمند'])->withInput()->with('show_edit_modal', true);
    }
}

    public function destroy(Employee $employee)
    {
        Gate::authorize('access', 'employees.delete');

        try {
            // حذف contact مرتبط (در صورت نیاز می‌توانید soft delete کنید)
            if ($employee->contact) {
                $employee->contact->delete();
            }
            $employee->delete();

            return redirect()->route('employees.index')->with('toast', [
                'message' => 'کارمند با موفقیت حذف شد.',
                'type'    => 'success',
                'title'   => 'حذف کارمند'
            ]);
        } catch (\Exception $e) {
            \Log::error($e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            return redirect()->back()
                ->withErrors(['error' => 'خطا در حذف کارمند']);
        }
    }
}
