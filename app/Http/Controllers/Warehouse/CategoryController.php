<?php

namespace App\Http\Controllers\Warehouse;

use App\Models\Category;
use App\Http\Requests\Warehouse\StoreCategoryRequest;
use App\Http\Requests\Warehouse\UpdateCategoryRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CategoryController extends BaseController
{
    public function index(Request $request)
    {
        Gate::authorize('access', 'product-categories.view');

        $tenantId = $this->manager->getTenantId();

        $allCategories = Category::where('tenant_id', $tenantId)->get();

        $query = Category::with('parent')->where('tenant_id', $tenantId);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('parent_id')) {
            $query->where('parent_id', $request->parent_id);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $query->latest();
        $categories = $query->paginate($request->per_page ?? 20);

        $stats = [
            'total'        => Category::where('tenant_id', $tenantId)->count(),
            'active'       => Category::where('tenant_id', $tenantId)->where('is_active', true)->count(),
            'inactive'     => Category::where('tenant_id', $tenantId)->where('is_active', false)->count(),
            'has_children' => Category::where('tenant_id', $tenantId)->whereHas('children')->count(),
        ];

        if ($request->ajax() || $request->input('ajax')) {
            return response()->json([
                'html'      => view('warehouse.categories._table', compact('categories'))->render(),
                'statsHtml' => view('warehouse.categories._stats', compact('stats'))->render(),
                'total'     => $categories->total(),
            ]);
        }

        return view('warehouse.categories.index', compact('categories', 'allCategories', 'stats'));
    }

    public function store(StoreCategoryRequest $request)
    {
        Gate::authorize('access', 'product-categories.create');

        try {
            $data = $request->validated();
            $data['tenant_id'] = $this->manager->getTenantId();

            Category::create($data);

            return redirect()->route('warehouse.categories.index')->with('toast', [
                'message' => 'دسته‌بندی با موفقیت ایجاد شد.',
                'type'    => 'success',
                'title'   => 'ایجاد دسته‌بندی'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('show_create_modal', true);
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'خطا در ایجاد دسته‌بندی: ' . $e->getMessage()])
                ->withInput()
                ->with('show_create_modal', true);
        }
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        Gate::authorize('access', 'product-categories.edit');

        try {
            $category->update($request->validated());

            return redirect()->route('warehouse.categories.index')->with('toast', [
                'message' => 'دسته‌بندی با موفقیت ویرایش شد.',
                'type'    => 'success',
                'title'   => 'ویرایش دسته‌بندی'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('show_edit_modal', true);
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'خطا در ویرایش دسته‌بندی: ' . $e->getMessage()])
                ->withInput()
                ->with('show_edit_modal', true);
        }
    }

    public function destroy(Category $category)
    {
        Gate::authorize('access', 'product-categories.delete');

        try {
            $category->delete();

            return redirect()->route('warehouse.categories.index')->with('toast', [
                'message' => 'دسته‌بندی با موفقیت حذف شد.',
                'type'    => 'success',
                'title'   => 'حذف دسته‌بندی'
            ]);
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'خطا در حذف دسته‌بندی: ' . $e->getMessage()]);
        }
    }
}