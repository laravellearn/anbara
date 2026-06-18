<?php

namespace App\Http\Controllers\Warehouse;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CategoryController extends BaseController
{

    public function index(Request $request)
    {
        Gate::authorize('access', 'product-categories.view');

        $tenantId = $this->manager->getTenantId();

        // برای انتخاب والد در فیلترها
        $allCategories = Category::where('tenant_id', $tenantId)->get();

        $query = Category::with('parent')->where('tenant_id', $tenantId);

        // جستجوی زنده
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // فیلتر والد
        if ($request->filled('parent_id')) {
            $query->where('parent_id', $request->parent_id);
        }

        // فیلتر وضعیت
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $query->latest();
        $categories = $query->paginate($request->per_page ?? 20);

        // کارت‌های آماری
        $stats = [
            'total'    => Category::where('tenant_id', $tenantId)->count(),
            'active'   => Category::where('tenant_id', $tenantId)->where('is_active', true)->count(),
            'inactive' => Category::where('tenant_id', $tenantId)->where('is_active', false)->count(),
        ];

        if ($request->ajax() || $request->input('ajax')) {
            $statsHtml = view('warehouse.categories._stats', compact('stats'))->render();
            $tableHtml = view('warehouse.categories._table', compact('categories'))->render();
            return response()->json([
                'html'      => $tableHtml,
                'statsHtml' => $statsHtml,
                'total'     => $categories->total(),
            ]);
        }

        return view('warehouse.categories.index', compact('categories', 'allCategories', 'stats'));
    }

    public function store(Request $request)
    {
        Gate::authorize('access', 'product-categories.create');

        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'parent_id'   => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'is_active'   => 'boolean',
        ]);

        $data['tenant_id'] = $this->manager->getTenantId();

        Category::create($data);

        flash()->success('دسته‌بندی ایجاد شد.');
        return redirect()->route('warehouse.categories.index');
    }

    public function update(Request $request, Category $category)
    {
        Gate::authorize('access', 'product-categories.edit');

        $category->update($request->validate([
            'title'       => 'required|string|max:255',
            'parent_id'   => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'is_active'   => 'boolean',
        ]));

        flash()->success('دسته‌بندی ویرایش شد.');
        return redirect()->route('warehouse.categories.index');
    }

    public function destroy(Category $category)
    {
        Gate::authorize('access', 'product-categories.delete');

        $category->delete();

        flash()->success('دسته‌بندی حذف شد.');
        return back();
    }
}
