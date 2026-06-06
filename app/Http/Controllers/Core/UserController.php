<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Company;
use App\Models\Role;
use App\Services\TenantManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

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

        $users = $query->latest()->paginate(20);
        $companies = Company::where('tenant_id', $tenantId)->get();
        $roles = Role::where('tenant_id', $tenantId)->get();

        return view('core.users.index', compact('users', 'companies', 'roles'));
    }

    /**
     * ذخیره کاربر جدید (از طریق مودال)
     */
    public function store(Request $request)
    {
        Gate::authorize('access', 'users.create');

        $request->validate([
            'name'            => 'required|string|max:255',
            'mobile'          => 'required|string|size:11|unique:users,mobile',
            'email'           => 'nullable|email|unique:users,email',
            'password'        => 'required|string|min:8|confirmed',
            'companies'       => 'required|array|min:1',
            'companies.*'     => 'exists:companies,id',
            'default_company' => 'required|in:' . implode(',', $request->companies),
            'roles'           => 'nullable|array',
            'roles.*'         => 'exists:roles,id',
        ]);

        $tenantId = $this->manager->getTenantId();

        $user = User::create([
            'tenant_id'  => $tenantId,
            'name'       => $request->name,
            'mobile'     => $request->mobile,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
            'is_active'  => true,
        ]);

        foreach ($request->companies as $companyId) {
            $user->companies()->attach($companyId, [
                'tenant_id'  => $tenantId,
                'is_default' => ($companyId == $request->default_company),
            ]);
        }

        $companyUser = $user->companyUsers()->where('company_id', $request->default_company)->first();
        if ($companyUser && $request->roles) {
            $companyUser->roles()->sync($request->roles);
        }

        return redirect()->route('users.index')->with('swal_success', 'کاربر با موفقیت ایجاد شد.');
    }

    /**
     * به‌روزرسانی کاربر (از طریق مودال)
     */
    public function update(Request $request, $id)
    {
        Gate::authorize('access', 'users.edit');

        $user = User::where('tenant_id', $this->manager->getTenantId())->findOrFail($id);

        $request->validate([
            'name'            => 'required|string|max:255',
            'mobile'          => ['required', 'string', 'size:11', Rule::unique('users')->ignore($user->id)],
            'email'           => ['nullable', 'email', Rule::unique('users')->ignore($user->id)],
            'password'        => 'nullable|string|min:8|confirmed',
            'companies'       => 'required|array|min:1',
            'companies.*'     => 'exists:companies,id',
            'default_company' => 'required|in:' . implode(',', $request->companies),
            'roles'           => 'nullable|array',
            'roles.*'         => 'exists:roles,id',
        ]);

        $tenantId = $this->manager->getTenantId();

        $user->update([
            'name'   => $request->name,
            'mobile' => $request->mobile,
            'email'  => $request->email,
        ]);

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        $syncData = [];
        foreach ($request->companies as $companyId) {
            $syncData[$companyId] = [
                'tenant_id'  => $tenantId,
                'is_default' => ($companyId == $request->default_company),
            ];
        }
        $user->companies()->sync($syncData);

        $companyUser = $user->companyUsers()->where('company_id', $request->default_company)->first();
        if ($companyUser) {
            $companyUser->roles()->sync($request->roles ?? []);
        }

        return redirect()->route('users.index')->with('swal_success', 'کاربر با موفقیت ویرایش شد.');
    }

    /**
     * غیرفعال‌سازی کاربر (Soft Delete)
     */
    public function destroy($id)
    {
        Gate::authorize('access', 'users.delete');

        $user = User::where('tenant_id', $this->manager->getTenantId())->findOrFail($id);
        $user->delete();

        return redirect()->route('users.index')->with('swal_success', 'کاربر غیرفعال شد.');
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

        return redirect()->route('users.index')->with('swal_success', 'عملیات ایمپورت با موفقیت آغاز شد.');
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
}