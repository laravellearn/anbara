<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    /**
     * لیست سازمان‌ها (با پشتیبانی از AJAX)
     */
    public function index(Request $request)
    {
        $tenant = auth()->user()->tenant;

        $query = $tenant->companies()->with('parent');

        // جستجوی زنده بر اساس نام
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // فیلتر وضعیت
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('isActive', true);
            } elseif ($request->status === 'inactive') {
                $query->where('isActive', false);
            }
        }

        // فیلتر سازمان والد (برای دیدن زیرمجموعه‌های یک سازمان خاص)
        if ($request->filled('parent_id')) {
            $query->where('parent_id', $request->parent_id);
        }

        // مرتب‌سازی
        $sort = $request->input('sort', 'created_at');
        $direction = $request->input('direction', 'desc');
        $allowedSorts = ['title', 'created_at', 'isActive'];
        if (in_array($sort, $allowedSorts)) {
            $query->orderBy($sort, $direction);
        }

        $perPage = $request->input('per_page', 20);
        $companies = $query->paginate($perPage)->appends($request->query());

        // اگر درخواست AJAX بود، فقط جدول را برگردان
        if ($request->ajax()) {
            $html = view('companies._table', compact('companies'))->render();
            return response()->json([
                'html'  => $html,
                'total' => $companies->total(),
            ]);
        }

        // داده‌های آماری برای کارت‌ها
        $stats = [
            'total'    => $tenant->companies()->count(),
            'active'   => $tenant->companies()->where('is_active', true)->count(),
            'inactive' => $tenant->companies()->where('is_active', false)->count(),
            'parents'  => $tenant->companies()->whereNull('parent_id')->count(),
        ];

        // لیست سازمان‌های والد برای dropdown فیلتر و مودال
        $parentCompanies = $tenant->companies()->whereNull('parent_id')->get();

        return view('core.companies.index', compact('companies', 'stats', 'parentCompanies'));
    }

    /**
     * ذخیره سازمان جدید
     */
    public function store(Request $request)
    {
        $tenant = auth()->user()->tenant;

        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'parent_id'   => 'nullable|exists:organizations,id',
            'isActive'    => 'sometimes|boolean',
        ]);

        // اطمینان از اینکه parent_id متعلق به همین tenant باشد
        if ($data['parent_id']) {
            $parent = Organization::findOrFail($data['parent_id']);
            if ($parent->tenant_id !== $tenant->id) {
                abort(403);
            }
        }

        $organization = $tenant->organizations()->create([
            'title'       => $data['title'],
            'description' => $data['description'] ?? null,
            'parent_id'   => $data['parent_id'] ?? null,
            'isActive'    => $request->has('isActive'),
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'سازمان جدید ایجاد شد.']);
        }

        return redirect()->route('companies.index')->with('success', 'سازمان جدید ایجاد شد.');
    }

    /**
     * ویرایش سازمان
     */
    public function update(Request $request, Organization $company)
    {
        $this->authorize('update', $company); // Policy اختیاری
        $tenant = auth()->user()->tenant;

        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'parent_id'   => 'nullable|exists:organizations,id',
            'isActive'    => 'sometimes|boolean',
        ]);

        // اطمینان از اینکه سازمان به Tenant جاری تعلق دارد
        if ($company->tenant_id !== $tenant->id) {
            abort(403);
        }

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

        $company->update([
            'title'       => $data['title'],
            'description' => $data['description'] ?? null,
            'parent_id'   => $data['parent_id'] ?? null,
            'isActive'    => $request->has('isActive'),
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'سازمان ویرایش شد.']);
        }

        return redirect()->route('companies.index')->with('success', 'سازمان ویرایش شد.');
    }

    /**
     * حذف سازمان
     */
    public function destroy(Organization $company)
    {
        $this->authorize('delete', $company);
        if ($company->tenant_id !== auth()->user()->tenant->id) {
            abort(403);
        }

        // اگر سازمان والد باشد و زیرمجموعه دارد، نمی‌توان حذف کرد (یا می‌توان policy گذاشت)
        if ($company->children()->exists()) {
            return back()->with('error', 'نمی‌توان سازمانی که زیرمجموعه دارد را حذف کرد.');
        }

        $company->delete();

        return redirect()->route('companies.index')->with('success', 'سازمان حذف شد.');
    }
}