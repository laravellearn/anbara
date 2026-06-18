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
            'total' => ProductType::where('tenant_id', $tenantId)->count(),
            'active' => ProductType::where('tenant_id', $tenantId)->where('is_active', true)->count(),
            'inactive' => ProductType::where('tenant_id', $tenantId)->where('is_active', false)->count(),
        ];
        if ($request->ajax()) {
            return response()->json([
                'html' => view('warehouse.product-types._table', compact('productTypes'))->render(),
                'statsHtml' => view('warehouse.product-types._stats', compact('stats'))->render(),
                'total' => $productTypes->total(),
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
        $data = $request->validate([
            'title'       => 'required|string|max:255|unique:product_types,title',
            'description' => 'nullable|string',
            'is_active'   => 'boolean',
            'attributes'  => 'nullable|array',
            'attributes.*.id'          => 'exists:product_attributes,id',
            'attributes.*.is_required' => 'boolean',
            'attributes.*.sort_order'  => 'integer|min:0',
        ]);
        $data['tenant_id'] = $this->manager->getTenantId();
        $data['company_id'] = $this->manager->getCompanyId();
        $productType = ProductType::create($data);

        // همگام‌سازی ویژگی‌ها
        if ($request->has('attributes')) {
            $sync = [];
            foreach ($request->attributes as $attr) {
                if (!empty($attr['id'])) {
                    $sync[$attr['id']] = [
                        'is_required' => $attr['is_required'] ?? false,
                        'sort_order'  => $attr['sort_order'] ?? 0,
                    ];
                }
            }
            $productType->attributes()->sync($sync);
        }

        flash()->success('نوع کالا ایجاد شد.');
        return redirect()->route('warehouse.product-types.index');
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
        $data = $request->validate([
            'title'       => 'required|string|max:255|unique:product_types,title,' . $productType->id,
            'description' => 'nullable|string',
            'is_active'   => 'boolean',
            'attributes'  => 'nullable|array',
            'attributes.*.id'          => 'exists:product_attributes,id',
            'attributes.*.is_required' => 'boolean',
            'attributes.*.sort_order'  => 'integer|min:0',
        ]);
        $productType->update($data);

        if ($request->has('attributes')) {
            $sync = [];
            foreach ($request->attributes as $attr) {
                if (!empty($attr['id'])) {
                    $sync[$attr['id']] = [
                        'is_required' => $attr['is_required'] ?? false,
                        'sort_order'  => $attr['sort_order'] ?? 0,
                    ];
                }
            }
            $productType->attributes()->sync($sync);
        }

        flash()->success('نوع کالا ویرایش شد.');
        return redirect()->route('warehouse.product-types.index');
    }

    public function destroy(ProductType $productType)
    {
        Gate::authorize('access', 'product-types.delete');
        $productType->delete();
        flash()->success('نوع کالا حذف شد.');
        return back();
    }

    // متد در ProductTypeController
    public function attributes(ProductType $productType, Request $request)
    {
        // اگر product_id ارسال شده، مقادیر قبلی را بگیریم
        $oldValues = collect();
        if ($request->has('product_id')) {
            $product = Product::find($request->product_id);
            if ($product) {
                $oldValues = $product->attributeValues->pluck('value', 'attribute_id');
            }
        }
        $html = view('warehouse.products._dynamic_attributes', [
            'attributes' => $productType->attributes,
            'oldValues'  => $oldValues,
        ])->render();
        return response()->json(['html' => $html]);
    }
}
