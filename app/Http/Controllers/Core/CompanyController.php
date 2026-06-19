<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Services\TenantManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

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

    public function store(Request $request)
    {
        Gate::authorize('access', 'companies.create');

        try {
            $data = $request->validate([
                'name'        => 'required|string|max:255',
                'description' => 'nullable|string',
                'parent_id'   => 'nullable|exists:companies,id',
                'is_active'   => 'sometimes|boolean',
            ]);

            // اطمینان از اینکه parent_id متعلق به همین tenant باشد
            if ($data['parent_id']) {
                $parent = Company::where('tenant_id', $this->manager->getTenantId())->findOrFail($data['parent_id']);
            }

            $data['tenant_id']  = $this->manager->getTenantId();
            $data['company_id'] = $this->manager->getCompanyId();  // خودش parent نیست؛ این برای AutoFill

            Company::create($data);

            return redirect()->route('core.companies.index')->with('toast', [
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

    public function update(Request $request, Company $company)
    {
        Gate::authorize('access', 'companies.edit');

        // اطمینان از تعلق به Tenant جاری
        if ($company->tenant_id !== $this->manager->getTenantId()) {
            abort(403);
        }

        try {
            $data = $request->validate([
                'name'        => 'required|string|max:255',
                'description' => 'nullable|string',
                'parent_id'   => 'nullable|exists:companies,id',
                'is_active'   => 'sometimes|boolean',
            ]);

            // جلوگیری از انتخاب خود یا زیرمجموعه‌ها به عنوان والد
            if ($data['parent_id']) {
                if ($data['parent_id'] == $company->id) {
                    return back()->withErrors(['parent_id' => 'سازمان نمی‌تواند والد خودش باشد.']);
                }
                $childrenIds = $company->children->pluck('id')->toArray();
                if (in_array($data['parent_id'], $childrenIds)) {
                    return back()->withErrors(['parent_id' => 'والد نمی‌تواند یکی از زیرمجموعه‌ها باشد.']);
                }
            }

            $company->update($data);

            return redirect()->route('core.companies.index')->with('toast', [
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

        try {
            if ($company->children()->exists()) {
                return back()->withErrors(['error' => 'نمی‌توان سازمانی که زیرمجموعه دارد را حذف کرد.']);
            }

            $company->delete();

            return redirect()->route('core.companies.index')->with('toast', [
                'message' => 'سازمان با موفقیت حذف شد.',
                'type'    => 'success',
                'title'   => 'حذف سازمان'
            ]);
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'خطا در حذف سازمان: ' . $e->getMessage()]);
        }
    }
}