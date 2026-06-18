<?php

namespace App\Http\Controllers\Warehouse;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CategoryController extends BaseController
{
    public function index()
    {
        Gate::authorize('access', 'product-categories.view');

        $categories = Category::with('parent')
            ->where('tenant_id', $this->manager->getTenantId())
            ->latest()
            ->paginate(20);

        $allCategories = Category::where('tenant_id', $this->manager->getTenantId())->get();

        return view('warehouse.categories.index', compact('categories', 'allCategories'));
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