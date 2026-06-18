<?php

namespace App\Http\Controllers\Warehouse;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\MeasurementUnit;
use App\Models\ProductAttribute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ProductController extends BaseController
{
    public function index()
    {
        Gate::authorize('access', 'products.view');

        $products = Product::with(['category', 'brand', 'baseMeasurementUnit'])
            ->where('tenant_id', $this->manager->getTenantId())
            ->latest()
            ->paginate(20);

        return view('warehouse.products.index', compact('products'));
    }

    public function create()
    {
        Gate::authorize('access', 'products.create');

        $categories       = Category::where('tenant_id', $this->manager->getTenantId())->get();
        $brands           = Brand::where('tenant_id', $this->manager->getTenantId())->get();
        $measurementUnits = MeasurementUnit::where('tenant_id', $this->manager->getTenantId())->get();
        $attributes       = ProductAttribute::where('tenant_id', $this->manager->getTenantId())->get();

        return view('warehouse.products.create', compact('categories', 'brands', 'measurementUnits', 'attributes'));
    }

    public function store(Request $request)
    {
        Gate::authorize('access', 'products.create');

        $data = $request->validate([
            'category_id'          => 'nullable|exists:categories,id',
            'brand_id'             => 'nullable|exists:brands,id',
            'measurement_unit_id'  => 'nullable|exists:measurement_units,id',
            'title'                => 'required|string|max:255',
            'sku'                  => 'nullable|string|max:50|unique:products,sku',
            'barcode'              => 'nullable|string|max:50',
            'model'                => 'nullable|string|max:255',
            'part_number'          => 'nullable|string|max:255',
            'description'          => 'nullable|string',
            'minimum_stock'        => 'nullable|numeric|min:0',
            'maximum_stock'        => 'nullable|numeric|min:0',
            'is_asset'             => 'boolean',
            'is_active'            => 'boolean',
            // واحدهای شمارشی اضافی
            'measurement_units'    => 'nullable|array',
            'measurement_units.*.id'              => 'exists:measurement_units,id',
            'measurement_units.*.conversion_factor' => 'nullable|numeric|min:0',
            'measurement_units.*.is_default'       => 'boolean',
            // ویژگی‌های دینامیک
            'attribute_values'     => 'nullable|array',
            'attribute_values.*.attribute_id' => 'exists:product_attributes,id',
            'attribute_values.*.value'        => 'nullable|string',
        ]);

        $data['tenant_id']  = $this->manager->getTenantId();
        $data['company_id'] = $this->manager->getCompanyId();

        $product = Product::create($data);

        // همگام‌سازی واحدهای شمارشی
        if ($request->has('measurement_units')) {
            $syncData = [];
            foreach ($request->measurement_units as $unit) {
                if (!empty($unit['id'])) {
                    $syncData[$unit['id']] = [
                        'conversion_factor' => $unit['conversion_factor'] ?? 1,
                        'is_default'        => $unit['is_default'] ?? false,
                        'company_id'        => $this->manager->getCompanyId(),
                    ];
                }
            }
            $product->measurementUnits()->sync($syncData);
        }

        // ذخیره مقادیر ویژگی‌ها
        if ($request->has('attribute_values')) {
            foreach ($request->attribute_values as $attr) {
                if (!empty($attr['attribute_id'])) {
                    $product->attributeValues()->updateOrCreate(
                        ['attribute_id' => $attr['attribute_id']],
                        ['value' => $attr['value'] ?? '']
                    );
                }
            }
        }

        flash()->success('کالا با موفقیت ایجاد شد.');
        return redirect()->route('warehouse.products.index');
    }

    public function edit(Product $product)
    {
        Gate::authorize('access', 'products.edit');

        $categories       = Category::where('tenant_id', $this->manager->getTenantId())->get();
        $brands           = Brand::where('tenant_id', $this->manager->getTenantId())->get();
        $measurementUnits = MeasurementUnit::where('tenant_id', $this->manager->getTenantId())->get();
        $attributes       = ProductAttribute::where('tenant_id', $this->manager->getTenantId())->get();

        $product->load('measurementUnits', 'attributeValues');

        return view('warehouse.products.edit', compact('product', 'categories', 'brands', 'measurementUnits', 'attributes'));
    }

    public function update(Request $request, Product $product)
    {
        Gate::authorize('access', 'products.edit');

        $data = $request->validate([
            'category_id'          => 'nullable|exists:categories,id',
            'brand_id'             => 'nullable|exists:brands,id',
            'measurement_unit_id'  => 'nullable|exists:measurement_units,id',
            'title'                => 'required|string|max:255',
            'sku'                  => 'nullable|string|max:50|unique:products,sku,' . $product->id,
            'barcode'              => 'nullable|string|max:50',
            'model'                => 'nullable|string|max:255',
            'part_number'          => 'nullable|string|max:255',
            'description'          => 'nullable|string',
            'minimum_stock'        => 'nullable|numeric|min:0',
            'maximum_stock'        => 'nullable|numeric|min:0',
            'is_asset'             => 'boolean',
            'is_active'            => 'boolean',
            'measurement_units'    => 'nullable|array',
            'measurement_units.*.id'              => 'exists:measurement_units,id',
            'measurement_units.*.conversion_factor' => 'nullable|numeric|min:0',
            'measurement_units.*.is_default'       => 'boolean',
            'attribute_values'     => 'nullable|array',
            'attribute_values.*.attribute_id' => 'exists:product_attributes,id',
            'attribute_values.*.value'        => 'nullable|string',
        ]);

        $product->update($data);

        if ($request->has('measurement_units')) {
            $syncData = [];
            foreach ($request->measurement_units as $unit) {
                if (!empty($unit['id'])) {
                    $syncData[$unit['id']] = [
                        'conversion_factor' => $unit['conversion_factor'] ?? 1,
                        'is_default'        => $unit['is_default'] ?? false,
                        'company_id'        => $this->manager->getCompanyId(),
                    ];
                }
            }
            $product->measurementUnits()->sync($syncData);
        }

        if ($request->has('attribute_values')) {
            foreach ($request->attribute_values as $attr) {
                if (!empty($attr['attribute_id'])) {
                    $product->attributeValues()->updateOrCreate(
                        ['attribute_id' => $attr['attribute_id']],
                        ['value' => $attr['value'] ?? '']
                    );
                }
            }
        }

        flash()->success('کالا با موفقیت ویرایش شد.');
        return redirect()->route('warehouse.products.index');
    }

    public function destroy(Product $product)
    {
        Gate::authorize('access', 'products.delete');

        $product->delete();

        flash()->success('کالا حذف شد.');
        return back();
    }
}