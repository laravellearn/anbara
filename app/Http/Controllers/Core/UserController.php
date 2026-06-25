<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\CompanyUser;
use App\Models\Employee;
use App\Models\Role;
use App\Models\User;
use App\Services\TenantManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    protected TenantManager $manager;

    public function __construct(TenantManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * لیست کاربران (با پشتیبانی از جستجوی زنده و فیلترها).
     */
    public function index(Request $request): \Illuminate\View\View|\Illuminate\Http\JsonResponse
    {
        Gate::authorize('access', 'users.view');

        $tenantId = $this->manager->getTenantId();

        $query = User::where('tenant_id', $tenantId)
            ->with(['companies', 'companyUsers.roles']);

        // جستجوی زنده
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('mobile', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // فیلترها
        if ($request->filled('company_id')) {
            $query->whereHas('companies', fn($q) => $q->where('company_id', $request->company_id));
        }
        if ($request->filled('role_id')) {
            $query->whereHas('companyUsers.roles', fn($q) => $q->where('role_id', $request->role_id));
        }
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        // مرتب‌سازی
        $sort      = $request->input('sort', 'created_at');
        $direction = $request->input('direction', 'desc');
        $allowed   = ['name', 'last_login_at', 'created_at'];
        $query->orderBy(in_array($sort, $allowed) ? $sort : 'created_at', $direction === 'asc' ? 'asc' : 'desc');

        $perPage = in_array((int) $request->input('per_page'), [10, 20, 50, 100]) ? (int) $request->input('per_page') : 20;
        $users   = $query->paginate($perPage)->appends($request->query());

        // داده‌های کمکی
        $companies = Company::where('tenant_id', $tenantId)->get();

        // نقش‌های قابل انتخاب برای فیلترها (همه نقش‌های tenant)
        $roles = Role::where('tenant_id', $tenantId)->get();

        // نقش‌های قابل انتخاب برای هر شرکت در مودال‌ها (عمومی + اختصاصی همان شرکت)
        $rolesByCompany = [];
        foreach ($companies as $company) {
            $rolesByCompany[$company->id] = Role::where('tenant_id', $tenantId)
                ->where(function ($q) use ($company) {
                    $q->whereNull('company_id')               // عمومی
                        ->orWhere('company_id', $company->id);  // اختصاصی این شرکت
                })
                ->get()
                ->map(fn($role) => ['id' => $role->id, 'title' => $role->title]);
        }

        if ($request->ajax()) {
            return response()->json([
                'html'  => view('core.users._table', compact('users'))->render(),
                'total' => $users->total(),
            ]);
        }

        return view('core.users.index', compact('users', 'companies', 'roles', 'rolesByCompany'));
    }

    /**
     * ذخیره کاربر جدید (از طریق مودال).
     */
    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        Gate::authorize('access', 'users.create');

        try {
            $validated = $request->validate([
                'name'            => 'required|string|max:255',
                'mobile'          => [
                    'required',
                    Rule::unique('users')->whereNotNull('mobile_verified_at'),
                    'regex:/^09[0-9]{9}$/',
                ],
                'email'           => ['nullable', 'email', 'unique:users,email'],
                'password'        => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
                'companies'       => 'required|array|min:1',
                'companies.*'     => 'exists:companies,id',
                'default_company' => 'required|in:' . implode(',', $request->companies),
                'company_roles'   => 'nullable|array',
                'company_roles.*' => 'array',
                'company_roles.*.*' => 'exists:roles,id',
                'create_employee' => 'nullable|boolean',
            ]);

            $tenantId = $this->manager->getTenantId();

            $user = User::create([
                'tenant_id' => $tenantId,
                'name'      => $validated['name'],
                'mobile'    => $validated['mobile'],
                'email'     => $validated['email'] ?? null,
                'password'  => Hash::make($validated['password']),
                'is_active' => $request->has('is_active'),
            ]);

            // اتصال به شرکت‌ها
            foreach ($validated['companies'] as $companyId) {
                $user->companies()->attach($companyId, [
                    'tenant_id'  => $tenantId,
                    'is_default' => ($companyId == $validated['default_company']),
                ]);
            }

            // ذخیره نقش‌های هر شرکت
            $companyRoles = $validated['company_roles'] ?? [];
            foreach ($companyRoles as $companyId => $roleIds) {
                if (!empty($roleIds) && in_array($companyId, $validated['companies'])) {
                    $companyUser = CompanyUser::where('user_id', $user->id)
                        ->where('company_id', $companyId)
                        ->first();
                    if ($companyUser) {
                        $companyUser->roles()->sync($roleIds);
                    }
                }
            }

            // ایجاد خودکار Employee (در صورت درخواست)
            if ($request->boolean('create_employee')) {
                Employee::create([
                    'tenant_id'   => $tenantId,
                    'company_id'  => $this->manager->getCompanyId(),
                    'user_id'     => $user->id,
                    'name'        => $user->name,
                    'employee_code' => $this->generateEmployeeCode(),
                    'is_active'   => true,
                ]);
            }

            return redirect()->route('users.index')->with('toast', [
                'message' => 'کاربر با موفقیت ایجاد شد',
                'type'    => 'success',
                'title'   => 'ایجاد کاربر',
            ]);
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput()->with('show_create_modal', true);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'خطا در ایجاد کاربر: ' . $e->getMessage()])->withInput()->with('show_create_modal', true);
        }
    }

    /**
     * به‌روزرسانی کاربر (از طریق مودال).
     */
    public function update(Request $request, $id): \Illuminate\Http\RedirectResponse
    {
        Gate::authorize('access', 'users.edit');

        $user = User::where('tenant_id', $this->manager->getTenantId())->findOrFail($id);

        try {
            $validated = $request->validate([
                'name'            => 'required|string|max:255',
                'mobile'          => [
                    'required',
                    Rule::unique('users')->ignore($user->id),
                    'regex:/^09[0-9]{9}$/',
                ],
                'email'           => ['nullable', 'email', Rule::unique('users')->ignore($user->id)],
                'password'        => ['nullable', 'confirmed', Password::min(8)->mixedCase()->numbers()],
                'companies'       => 'required|array|min:1',
                'companies.*'     => 'exists:companies,id',
                'default_company' => 'required|in:' . implode(',', $request->companies),
                'company_roles'   => 'nullable|array',
                'company_roles.*' => 'array',
                'company_roles.*.*' => 'exists:roles,id',
            ]);

            $tenantId = $this->manager->getTenantId();

            $updateData = [
                'name'      => $validated['name'],
                'mobile'    => $validated['mobile'],
                'email'     => $validated['email'] ?? null,
                'is_active' => $request->has('is_active'),
            ];

            if (!empty($validated['password'])) {
                $updateData['password'] = Hash::make($validated['password']);
            }

            $user->update($updateData);

            // همگام‌سازی شرکت‌ها
            $syncData = [];
            foreach ($validated['companies'] as $companyId) {
                $syncData[$companyId] = [
                    'tenant_id'  => $tenantId,
                    'is_default' => ($companyId == $validated['default_company']),
                ];
            }
            $user->companies()->sync($syncData);

            // به‌روزرسانی نقش‌ها (حذف همه، سپس افزودن جدید)
            $userCompanyIds = CompanyUser::where('user_id', $user->id)->pluck('id');
            DB::table('company_user_role')->whereIn('company_user_id', $userCompanyIds)->delete();

            $companyRoles = $validated['company_roles'] ?? [];
            foreach ($companyRoles as $companyId => $roleIds) {
                if (!empty($roleIds) && in_array($companyId, $validated['companies'])) {
                    $companyUser = CompanyUser::where('user_id', $user->id)
                        ->where('company_id', $companyId)
                        ->first();
                    if ($companyUser) {
                        $companyUser->roles()->sync($roleIds);
                    }
                }
            }

            return redirect()->route('users.index')->with('toast', [
                'message' => 'کاربر با موفقیت ویرایش شد',
                'type'    => 'success',
                'title'   => 'ویرایش کاربر',
            ]);
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput()->with('show_edit_modal', true);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'خطا در ویرایش کاربر: ' . $e->getMessage()])->withInput()->with('show_edit_modal', true);
        }
    }

    /**
     * غیرفعال‌سازی کاربر (Soft Delete).
     */
    public function destroy($id): \Illuminate\Http\RedirectResponse
    {
        Gate::authorize('access', 'users.delete');

        $user = User::where('tenant_id', $this->manager->getTenantId())->findOrFail($id);
        $user->delete();

        return redirect()->route('users.index')->with('toast', [
            'message' => 'کاربر با موفقیت حذف شد',
            'type'    => 'success',
            'title'   => 'حذف کاربر',
        ]);
    }

    /**
     * ایمپورت کاربران از فایل Excel.
     */
    public function import(Request $request): \Illuminate\Http\RedirectResponse
    {
        Gate::authorize('access', 'users.import');
        $request->validate(['file' => 'required|file|mimes:xlsx,xls,csv|max:10240']);

        // TODO: پیاده‌سازی واقعی
        return redirect()->route('users.index')->with('toast', [
            'message' => 'عملیات ایمپورت با موفقیت آغاز شد.',
            'type'    => 'success',
            'title'   => 'ایمپورت کاربران',
        ]);
    }

    /**
     * خروجی Excel از لیست کاربران.
     */
    public function export(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        Gate::authorize('access', 'users.export');

        $tenantId = $this->manager->getTenantId();
        $users    = User::where('tenant_id', $tenantId)->with('companies')->get();

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="users_export_' . date('YmdHis') . '.csv"',
        ];

        $callback = function () use ($users) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM UTF-8
            fputcsv($handle, ['نام', 'موبایل', 'ایمیل', 'شرکت‌ها', 'وضعیت']);
            foreach ($users as $user) {
                fputcsv($handle, [
                    $user->name,
                    $user->mobile,
                    $user->email,
                    $user->companies->pluck('name')->implode(', '),
                    $user->is_active ? 'فعال' : 'غیرفعال',
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * تولید کد پرسنلی یکتا.
     */
    private function generateEmployeeCode(): string
    {
        do {
            $code = 'EMP-' . random_int(1000, 9999);
        } while (Employee::where('employee_code', $code)->exists());

        return $code;
    }
}
