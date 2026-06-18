<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use App\Services\TenantManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ProductCategoryController extends Controller
{
    protected $manager;

    public function __construct(TenantManager $manager)
    {
        $this->manager = $manager;
    }

    public function index()
    {
        Gate::authorize('access', 'product-categories.view');

        $categories = ProductCategory::where('tenant_id', $this->manager->getTenantId())
            ->with('parent')
            ->latest()
            ->paginate(20);

        // برای انتخاب والد در مودال
        $allCategories = ProductCategory::where('tenant_id', $this->manager->getTenantId())->get();

        return view('warehouse.product-categories.index', compact('categories', 'allCategories'));
    }

    public function store(Request $request)
    {
        Gate::authorize('access', 'product-categories.create');

        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'parent_id'   => 'nullable|exists:product_categories,id',
            'description' => 'nullable|string',
            'is_active'   => 'boolean',
        ]);

        $data['tenant_id'] = $this->manager->getTenantId();
        ProductCategory::create($data);

        flash()->success('دسته‌بندی جدید ایجاد شد.');
        return redirect()->route('warehouse.product-categories.index');
    }

    public function update(Request $request, ProductCategory $category)
    {
        Gate::authorize('access', 'product-categories.edit');

        $category->update($request->validate([
            'name'        => 'required|string|max:255',
            'parent_id'   => 'nullable|exists:product_categories,id',
            'description' => 'nullable|string',
            'is_active'   => 'boolean',
        ]));

        flash()->success('دسته‌بندی ویرایش شد.');
        return redirect()->route('warehouse.product-categories.index');
    }

    public function destroy(ProductCategory $category)
    {
        Gate::authorize('access', 'product-categories.delete');

        $category->delete();
        flash()->success('دسته‌بندی حذف شد.');
        return back();
    }
}