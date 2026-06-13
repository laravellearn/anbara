<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Models\CompanyUser;
use App\Models\User;
use App\Models\Company;
use App\Models\Role;
use App\Services\TenantManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    protected $manager;

    public function __construct(TenantManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * لیست کاربران (با فیلترها و اطلاعات کمکی برای مودال‌ها)
     */
    public function index()
    {
        Gate::authorize('access', 'users.view');

        $tenantId = $this->manager->getTenantId();

        $query = User::where('tenant_id', $tenantId)->with(['companies', 'companyUsers.roles']);

        // جستجوی زنده
        if (request('search')) {
            $search = request('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('mobile', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // فیلترها
        if (request('company_id')) {
            $query->whereHas('companies', fn($q) => $q->where('company_id', request('company_id')));
        }
        if (request('role_id')) {
            $query->whereHas('companyUsers.roles', fn($q) => $q->where('role_id', request('role_id')));
        }
        if (request('status') === 'active') {
            $query->where('is_active', true);
        } elseif (request('status') === 'inactive') {
            $query->where('is_active', false);
        }

        // مرتب‌سازی
        $allowedSorts = ['name', 'last_login_at', 'created_at'];
        $sortBy = in_array(request('sort'), $allowedSorts) ? request('sort') : 'created_at';
        $sortDir = request('direction', 'desc') === 'asc' ? 'asc' : 'desc';
        $query->orderBy($sortBy, $sortDir);

        $perPage = in_array(request('per_page'), [10, 20, 50, 100]) ? (int) request('per_page') : 20;
        $users = $query->paginate($perPage)->appends(request()->query());

        // همیشه این دو متغیر را بگیر - حتی برای AJAX
        $companies = Company::where('tenant_id', $tenantId)->get();
        $roles = Role::where('tenant_id', $tenantId)->get();

        if (request()->ajax()) {
            return response()->json([
                'html' => view('core.users._table', compact('users'))->render(),
                'total' => $users->total(),
            ]);
        }

        return view('core.users.index', compact('users', 'companies', 'roles'));
    }

    /**
     * ذخیره کاربر جدید (از طریق مودال)
     */
    /**
     * ذخیره کاربر جدید (از طریق مودال)
     */
    public function store(Request $request)
    {
        Gate::authorize('access', 'users.create');

        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'mobile' => [
                    'required',
                    Rule::unique('users')
                        ->whereNotNull('mobile_verified_at'),
                    'regex:/^09[0-9]{9}$/',
                ],

                'email' => [
                    'nullable',
                    'email',
                    'unique:users,email'
                ],
                'password' => [
                    'required',
                    'confirmed',
                    Password::min(8)
                        ->mixedCase()
                        ->numbers()
                ],
                'companies' => 'required|array|min:1',
                'companies.*' => 'exists:companies,id',
                'default_company' => 'required|in:' . implode(',', $request->companies),
                'company_roles' => 'nullable|array',
                'company_roles.*' => 'array',
                'company_roles.*.*' => 'exists:roles,id',
            ]);

            $tenantId = $this->manager->getTenantId();

            $user = User::create([
                'tenant_id' => $tenantId,
                'name' => $request->name,
                'mobile' => $request->mobile,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'is_active' => $request->has('is_active'),
            ]);

            // اتصال به شرکت‌ها
            foreach ($request->companies as $companyId) {
                $user->companies()->attach($companyId, [
                    'tenant_id' => $tenantId,
                    'is_default' => ($companyId == $request->default_company),
                ]);
            }

            // ذخیره نقش‌های هر شرکت
            $companyRoles = $request->company_roles ?? [];
            foreach ($companyRoles as $companyId => $roleIds) {
                if (!empty($roleIds) && in_array($companyId, $request->companies)) {
                    $companyUser = CompanyUser::where('user_id', $user->id)
                        ->where('company_id', $companyId)
                        ->first();

                    if ($companyUser) {
                        $companyUser->roles()->sync($roleIds);
                    }
                }
            }

            return redirect()->route('users.index')->with('toast', [
                'message' => 'کاربر با موفقیت ایجاد شد',
                'type' => 'success',
                'title' => 'ایجاد کاربر'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // برگرداندن خطاها به صفحه قبل با حفظ داده‌های فرم
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('show_create_modal', true);
        } catch (\Exception $e) {
            // سایر خطاها
            return redirect()->back()
                ->withErrors(['error' => 'خطا در ایجاد کاربر: ' . $e->getMessage()])
                ->withInput()
                ->with('show_create_modal', true);
        }
    }

    /**
     * به‌روزرسانی کاربر (از طریق مودال)
     */
    public function update(Request $request, $id)
    {
        Gate::authorize('access', 'users.edit');

        $user = User::where('tenant_id', $this->manager->getTenantId())->findOrFail($id);


        $currentUser = auth()->user();

        // ========== 1. جلوگیری از غیرفعال کردن کاربر لاگین شده ==========
        if ($id == $currentUser->id) {
            // اگر کاربر سعی در غیرفعال کردن خودش دارد
            if (!$request->is_active) {
                return redirect()->back()
                    ->withErrors(['is_active' => 'شما نمی‌توانید حساب کاربری خود را غیرفعال کنید.'])
                    ->withInput()
                    ->with('show_edit_modal', true);
            }

            // اگر کاربر سعی در تغییر نقش خودش دارد (اختیاری)
            // می‌توانید اینجا چک کنید
        }

        // ========== 2. جلوگیری از غیرفعال کردن کاربران دارای نقش admin یا manager ==========
        // بررسی کنید کاربر هدف دارای نقش ادمین یا مدیر است
        $targetUserRoles = $user->companyUsers()
            ->where('is_default', true)
            ->first()?->roles->pluck('code')->toArray() ?? [];

        $protectedRoles = ['tenant_admin']; // نقش‌های محافظت شده

        $hasProtectedRole = !empty(array_intersect($protectedRoles, $targetUserRoles));

        // کاربر فعلی ادمین نیست و می‌خواهد کاربر با نقش محافظت شده را غیرفعال کند
        $currentUserRoles = $currentUser->companyUsers()
            ->where('is_default', true)
            ->first()?->roles->pluck('code')->toArray() ?? [];

        $isCurrentUserAdmin = in_array('admin', $currentUserRoles) || in_array('super-admin', $currentUserRoles);

        if ($hasProtectedRole && !$isCurrentUserAdmin && $user->id != $currentUser->id) {
            if ($request->has('is_active') && !$request->is_active) {
                return redirect()->back()
                    ->withErrors(['is_active' => 'شما نمی‌توانید کاربران مدیر را غیرفعال کنید.'])
                    ->withInput()
                    ->with('show_edit_modal', true);
            }
        }


        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'mobile' => [
                    'required',
                    Rule::unique('users')
                        ->whereNotNull('mobile_verified_at'),
                    'regex:/^09[0-9]{9}$/',
                ],

                'email' => [
                    'nullable',
                    'email',
                    'unique:users,email'
                ],
                'password' => [
                    'required',
                    'confirmed',
                    Password::min(8)
                        ->mixedCase()
                        ->numbers()
                ],
                'companies' => 'required|array|min:1',
                'companies.*' => 'exists:companies,id',
                'default_company' => 'required|in:' . implode(',', $request->companies),
                'company_roles' => 'nullable|array',
                'company_roles.*' => 'array',
                'company_roles.*.*' => 'exists:roles,id',
            ]);

            $tenantId = $this->manager->getTenantId();

            // آپدیت اطلاعات پایه
            $updateData = [
                'name' => $request->name,
                'mobile' => $request->mobile,
                'email' => $request->email,
                'is_active' => $request->has('is_active'),
            ];

            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            $user->update($updateData);

            // همگام‌سازی شرکت‌ها
            $syncData = [];
            foreach ($request->companies as $companyId) {
                $syncData[$companyId] = [
                    'tenant_id' => $tenantId,
                    'is_default' => ($companyId == $request->default_company),
                ];
            }
            $user->companies()->sync($syncData);

            // پاک کردن همه نقش‌های قبلی در company_user_role
            $userCompanyIds = CompanyUser::where('user_id', $user->id)->pluck('id');
            \DB::table('company_user_role')->whereIn('company_user_id', $userCompanyIds)->delete();

            // ذخیره نقش‌های جدید برای هر شرکت
            $companyRoles = $request->company_roles ?? [];
            foreach ($companyRoles as $companyId => $roleIds) {
                if (!empty($roleIds) && in_array($companyId, $request->companies)) {
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
                'type' => 'success',
                'title' => 'ویرایش کاربر'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // برگرداندن خطاهای اعتبارسنجی
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('show_edit_modal', true);
        } catch (\Exception $e) {
            // سایر خطاها
            return redirect()->back()
                ->withErrors(['error' => 'خطا در ویرایش کاربر: ' . $e->getMessage()])
                ->withInput()
                ->with('show_edit_modal', true);
        }
    }
    /**
     * غیرفعال‌سازی کاربر (Soft Delete)
     */
    public function destroy($id)
    {
        Gate::authorize('access', 'users.delete');

        $user = User::where('tenant_id', $this->manager->getTenantId())->findOrFail($id);
        $user->delete();
        toast('کاربر با موفقیت حذف شد', 'success');

        return redirect()->route('users.index');
    }

    /**
     * ایمپورت کاربران از فایل Excel
     */
    public function import(Request $request)
    {
        Gate::authorize('access', 'users.import');

        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ]);

        // TODO: پیاده‌سازی واقعی با استفاده از پکیج Laravel Excel
        // $request->file('file')->store('imports');
        // (Logic: خواندن فایل، اعتبارسنجی سطری، رعایت محدودیت max_users و ایجاد کاربران)
        toast('عملیات ایمپورت با موفقیت آغاز شد.', 'success');

        return redirect()->route('users.index');
    }

    /**
     * خروجی Excel از لیست کاربران
     */
    public function export()
    {
        Gate::authorize('access', 'users.export');

        $tenantId = $this->manager->getTenantId();
        $users = User::where('tenant_id', $tenantId)->with('companies')->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
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

    public function show(User $user)
    {
        Gate::authorize('access', 'users.view');

        $tenantId = $this->manager->getTenantId();

        if (!auth()->user()->isSuperAdmin()) {
            if ($user->tenant_id != $tenantId) {
                abort(403);
            }
        }

        $user->load(['companies', 'companyUsers.roles']);

        $defaultCompanyUser = $user->companyUsers()->where('is_default', true)->first();

        // تعریف متغیرها برای مودال‌ها
        $companies = Company::where('tenant_id', $tenantId)->get();
        $roles = Role::where('tenant_id', $tenantId)->get();

        return view('core.users.show', compact('user', 'defaultCompanyUser', 'companies', 'roles'));
    }
}
