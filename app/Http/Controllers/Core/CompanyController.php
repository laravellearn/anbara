<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Http\Requests\Core\StoreCompanyRequest;
use App\Http\Requests\Core\UpdateCompanyRequest;
use App\Services\TenantManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CompanyController extends Controller
{
    protected TenantManager $manager;

    public function __construct(TenantManager $manager)
    {
        $this->manager = $manager;
    }

    public function index(Request $request)
    {
        Gate::authorize('access', 'companies.view');

        $tenantId = $this->manager->getTenantId();

        $query = Company::with('parent')->where('tenant_id', $tenantId);

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        if ($request->filled('parent_id')) {
            $query->where('parent_id', $request->parent_id);
        }

        $sort = $request->input('sort', 'created_at');
        $direction = $request->input('direction', 'desc');
        $allowedSorts = ['name', 'created_at', 'is_active'];
        if (in_array($sort, $allowedSorts)) {
            $query->orderBy($sort, $direction);
        }

        $perPage = $request->input('per_page', 20);
        $companies = $query->paginate($perPage)->appends($request->query());

        if ($request->ajax()) {
            $html = view('core.companies._table', compact('companies'))->render();
            return response()->json([
                'html'  => $html,
                'total' => $companies->total(),
            ]);
        }

        $stats = [
            'total'    => Company::where('tenant_id', $tenantId)->count(),
            'active'   => Company::where('tenant_id', $tenantId)->where('is_active', true)->count(),
            'inactive' => Company::where('tenant_id', $tenantId)->where('is_active', false)->count(),
            'parents'  => Company::where('tenant_id', $tenantId)->whereNull('parent_id')->count(),
        ];

        $parentCompanies = Company::where('tenant_id', $tenantId)->whereNull('parent_id')->get();

        return view('core.companies.index', compact('companies', 'stats', 'parentCompanies'));
    }

    public function store(StoreCompanyRequest $request)
    {
        Gate::authorize('access', 'companies.create');

        try {
            $data = $request->validated();

            // کد خودکار
            if (empty($data['code'])) {
                do {
                    $code = 'ORG-' . strtoupper(Str::random(6));
                } while (Company::where('code', $code)->exists());
                $data['code'] = $code;
            }

            $data['tenant_id']  = $this->manager->getTenantId();
            $data['type'] ??= 'company';

            // آپلود لوگو در صورت ارسال؛ در غیر این صورت کلید logo اصلاً به $data اضافه نمی‌شود
            $logoPath = $this->storeLogo($request);
            if ($logoPath !== null) {
                $data['logo'] = $logoPath;
            } else {
                unset($data['logo']);
            }

            $company = Company::create($data);

            // ✅ کاربر جاری را عضو این سازمان کن
            $company->users()->attach(auth()->id(), [
                'tenant_id'  => $this->manager->getTenantId(),
                'is_default' => false,
            ]);

            // اختصاص نقش tenant_admin به کاربر در این شرکت جدید
            $adminRole = \App\Models\Role::where('tenant_id', $this->manager->getTenantId())
                ->where('code', 'tenant_admin')
                ->first();

            if ($adminRole) {
                $companyUser = \App\Models\CompanyUser::where('company_id', $company->id)
                    ->where('user_id', auth()->id())
                    ->first();

                if ($companyUser) {
                    $companyUser->roles()->attach($adminRole->id);
                }
            }

            return redirect()->route('companies.index')->with('toast', [
                'message' => 'سازمان جدید با موفقیت ایجاد شد.',
                'type'    => 'success',
                'title'   => 'ایجاد سازمان'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('show_create_modal', true);
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'خطا در ایجاد سازمان: ' . $e->getMessage()])
                ->withInput()
                ->with('show_create_modal', true);
        }
    }

    public function update(UpdateCompanyRequest $request, Company $company)
    {
        Gate::authorize('access', 'companies.edit');

        if ($company->tenant_id !== $this->manager->getTenantId()) {
            abort(403);
        }

        try {
            $data = $request->validated();

            // ========== مدیریت دقیق لوگو ==========
            $logoPath = $this->storeLogo($request);

            if ($logoPath !== null) {
                // حذف لوگوی قبلی (در صورت وجود) فقط وقتی لوگوی جدید با موفقیت ذخیره شد
                if ($company->logo) {
                    Storage::disk('public')->delete($company->logo);
                }
                $data['logo'] = $logoPath;
            } else {
                // کاربر لوگوی جدیدی نفرستاده؛ لوگوی فعلی دست‌نخورده باقی می‌ماند
                unset($data['logo']);
            }

            $company->update($data);

            return redirect()->route('companies.index')->with('toast', [
                'message' => 'سازمان با موفقیت ویرایش شد.',
                'type'    => 'success',
                'title'   => 'ویرایش سازمان'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('show_edit_modal', true);
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'خطا در ویرایش سازمان: ' . $e->getMessage()])
                ->withInput()
                ->with('show_edit_modal', true);
        }
    }

    public function destroy(Company $company)
    {
        Gate::authorize('access', 'companies.delete');

        if ($company->tenant_id !== $this->manager->getTenantId()) {
            abort(403);
        }
        // جلوگیری از حذف سازمان جاری
        if ($company->id === $this->manager->getCompanyId()) {
            return back()->withErrors(['error' => 'نمی‌توانید سازمان جاری را حذف کنید. ابتدا به سازمان دیگری بروید.']);
        }

        try {
            if ($company->children()->exists()) {
                return back()->withErrors(['error' => 'نمی‌توان سازمانی که زیرمجموعه دارد را حذف کرد.']);
            }

            $company->delete();

            return redirect()->route('companies.index')->with('toast', [
                'message' => 'سازمان با موفقیت حذف شد.',
                'type'    => 'success',
                'title'   => 'حذف سازمان'
            ]);
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'خطا در حذف سازمان: ' . $e->getMessage()]);
        }
    }

    /**
     * آپلود ایمن فایل لوگو از request جاری.
     *
     * اگر فایلی ارسال نشده باشد یا فایل ارسالی نامعتبر/خالی باشد، null
     * برمی‌گرداند تا کنترلر بفهمد باید لوگوی فعلی را دست‌نخورده بگذارد.
     *
     * علت اصلی خطای «Path cannot be empty» در نسخه‌ی قبلی این متد این بود
     * که getClientOriginalExtension() در بعضی حالت‌ها (آپلود از طریق برخی
     * مرورگرها/کلاینت‌ها بدون پسوند مشخص، یا یک فایل موقت خراب) رشته‌ی
     * خالی برمی‌گرداند. این متد، آن حالت را صریحاً تشخیص می‌دهد و به‌جای
     * تولید نام فایل ناقص، با یک پسوند پیش‌فرض امن (png) جلوگیری می‌کند.
     * علاوه بر آن، خروجی storeAs() نیز بررسی می‌شود؛ اگر ذخیره‌سازی به هر
     * دلیلی (دیسک، دسترسی، یا فضای ناکافی) شکست بخورد، یک استثنای واضح
     * پرتاب می‌شود به‌جای ذخیره‌ی مقدار false در دیتابیس.
     */
    protected function storeLogo(Request $request): ?string
    {
        if (! $request->hasFile('logo')) {
            return null;
        }

        $uploadedLogo = $request->file('logo');

        if (! $uploadedLogo || ! $uploadedLogo->isValid() || $uploadedLogo->getSize() <= 0) {
            return null;
        }

        $extension = $uploadedLogo->getClientOriginalExtension() ?: 'png';
        $filename  = Str::uuid() . '.' . $extension;
        $path      = 'companies/logos/' . $filename;

        try {
            // خواندن مستقیم محتوای فایل (بدون نیاز به realPath)
            $content = file_get_contents($uploadedLogo->getPathname());

            if ($content === false) {
                throw new \RuntimeException('خواندن فایل لوگو ممکن نیست.');
            }

            // ذخیره در دیسک public با استفاده از Storage::put
            $stored = Storage::disk('public')->put($path, $content);

            if (! $stored) {
                throw new \RuntimeException('ذخیره‌سازی لوگو با خطا مواجه شد.');
            }

            return $path;
        } catch (\Exception $e) {
            \Log::error('آپلود لوگو ناموفق', ['error' => $e->getMessage()]);
            return null;
        }
    }
}
