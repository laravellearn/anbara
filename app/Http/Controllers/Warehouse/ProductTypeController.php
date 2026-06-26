<?php

namespace App\Http\Controllers\Warehouse;

use App\Models\Product;
use App\Models\ProductType;
use App\Models\ProductAttribute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ProductTypeController extends BaseController
{
    public function index(Request $request)
    {
        Gate::authorize('access', 'product-types.view');
        $tenantId = $this->manager->getTenantId();

        $query = ProductType::where('tenant_id', $tenantId);
        if ($request->filled('search')) {
            $query->where('title', 'like', "%{$request->search}%");
        }
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }
        $productTypes = $query->paginate($request->per_page ?? 20);

        $stats = [
            'total'    => ProductType::where('tenant_id', $tenantId)->count(),
            'active'   => ProductType::where('tenant_id', $tenantId)->where('is_active', true)->count(),
            'inactive' => ProductType::where('tenant_id', $tenantId)->where('is_active', false)->count(),
        ];

        if ($request->ajax() || $request->input('ajax')) {
            return response()->json([
                'html'      => view('warehouse.product-types._table', compact('productTypes'))->render(),
                'statsHtml' => view('warehouse.product-types._stats', compact('stats'))->render(),
                'total'     => $productTypes->total(),
            ]);
        }

        return view('warehouse.product-types.index', compact('productTypes', 'stats'));
    }

    public function create()
    {
        Gate::authorize('access', 'product-types.create');
        $attributes = ProductAttribute::where('tenant_id', $this->manager->getTenantId())->get();
        return view('warehouse.product-types.create', compact('attributes'));
    }

    public function store(Request $request)
    {
        Gate::authorize('access', 'product-types.create');

        try {
            $validated = $request->validate([
                'title'       => 'required|string|max:255|unique:product_types,title',
                'description' => 'nullable|string',
                'is_active'   => 'boolean',
                'attributes'  => 'nullable|array',
                'attributes.*.id'          => 'exists:product_attributes,id',
                'attributes.*.is_required' => 'boolean',
                'attributes.*.sort_order'  => 'integer|min:0',
            ]);

            // حذف ویژگی‌ها از داده‌های مدل اصلی
            $productTypeData = collect($validated)->except('attributes')->toArray();
            $productTypeData['tenant_id']  = $this->manager->getTenantId();
            $productTypeData['company_id'] = $this->manager->getCompanyId();
            $productTypeData['is_active']  = $request->boolean('is_active', false);

            $productType = ProductType::create($productTypeData);

            // همگام‌سازی ویژگی‌ها با استفاده از داده‌های اعتبارسنجی‌شده
            $sync = [];
            if (!empty($validated['attributes'])) {
                foreach ($validated['attributes'] as $attrId => $attrData) {
                    if (!empty($attrData['id'])) {
                        $sync[$attrData['id']] = [
                            'is_required' => $attrData['is_required'] ?? false,
                            'sort_order'  => $attrData['sort_order'] ?? 0,
                        ];
                    }
                }
            }
            $productType->attributes()->sync($sync);

            return redirect()->route('warehouse.product-types.index')->with('toast', [
                'message' => 'نوع کالا با موفقیت ایجاد شد.',
                'type'    => 'success',
                'title'   => 'ایجاد نوع کالا'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'خطا در ایجاد نوع کالا: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function edit(ProductType $productType)
    {
        Gate::authorize('access', 'product-types.edit');
        $attributes = ProductAttribute::where('tenant_id', $this->manager->getTenantId())->get();
        $productType->load('attributes');
        return view('warehouse.product-types.edit', compact('productType', 'attributes'));
    }

    public function update(Request $request, ProductType $productType)
    {
        Gate::authorize('access', 'product-types.edit');

        try {
            $validated = $request->validate([
                'title'       => 'required|string|max:255|unique:product_types,title,' . $productType->id,
                'description' => 'nullable|string',
                'is_active'   => 'boolean',
                'attributes'  => 'nullable|array',
                'attributes.*.id'          => 'exists:product_attributes,id',
                'attributes.*.is_required' => 'boolean',
                'attributes.*.sort_order'  => 'integer|min:0',
            ]);

            $productTypeData = collect($validated)->except('attributes')->toArray();
            $productTypeData['is_active'] = $request->boolean('is_active', false);
            $productType->update($productTypeData);

            $sync = [];
            if (!empty($validated['attributes'])) {
                foreach ($validated['attributes'] as $attrId => $attrData) {
                    if (!empty($attrData['id'])) {
                        $sync[$attrData['id']] = [
                            'is_required' => $attrData['is_required'] ?? false,
                            'sort_order'  => $attrData['sort_order'] ?? 0,
                        ];
                    }
                }
            }
            $productType->attributes()->sync($sync);

            return redirect()->route('warehouse.product-types.index')->with('toast', [
                'message' => 'نوع کالا با موفقیت ویرایش شد.',
                'type'    => 'success',
                'title'   => 'ویرایش نوع کالا'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'خطا در ویرایش نوع کالا: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function destroy(ProductType $productType)
    {
        Gate::authorize('access', 'product-types.delete');

        try {
            $productType->delete();
            return redirect()->route('warehouse.product-types.index')->with('toast', [
                'message' => 'نوع کالا با موفقیت حذف شد.',
                'type'    => 'success',
                'title'   => 'حذف نوع کالا'
            ]);
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'خطا در حذف نوع کالا: ' . $e->getMessage()]);
        }
    }

    // متد AJAX برای برگرداندن ویژگی‌های نوع کالا
    public function attributes(ProductType $productType, Request $request)
    {
        $oldValues = collect();
        if ($request->has('product_id')) {
            $product = Product::find($request->product_id);
            if ($product) {
                $oldValues = $product->attributeValues->pluck('value', 'attribute_id');
            }
        }

        $html = view('warehouse.products._dynamic_attributes', [
            'attributes' => $productType->attributes()->orderByPivot('sort_order')->get(),
            'oldValues'  => $oldValues,
        ])->render();

        return response()->json(['html' => $html]);
    }
}