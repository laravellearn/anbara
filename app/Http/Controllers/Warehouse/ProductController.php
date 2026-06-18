<?php

namespace App\Http\Controllers\Warehouse;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\MeasurementUnit;
use App\Models\ProductAttribute;
use App\Models\ProductType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ProductController extends BaseController
{

    public function index(Request $request)
    {
        Gate::authorize('access', 'products.view');

        $tenantId = $this->manager->getTenantId();
        $companyId = $this->manager->getCompanyId();

        // داده‌های مورد نیاز فیلترها
        $categories = Category::where('tenant_id', $tenantId)->get();
        $brands     = Brand::where('tenant_id', $tenantId)->get();

        // پایهٔ query
        $query = Product::with(['category', 'brand', 'baseMeasurementUnit'])
            ->where('tenant_id', $tenantId);

        // اعمال فیلترها
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('barcode', 'like', "%{$search}%");
            });
        }
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }
        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->input('brand_id'));
        }
        if ($request->filled('status')) {
            $query->where('is_active', $request->input('status') === 'active');
        }

        // مرتب‌سازی
        $sort = $request->input('sort', 'created_at');
        $direction = $request->input('direction', 'desc');
        $allowedSorts = ['title', 'created_at', 'minimum_stock'];
        if (in_array($sort, $allowedSorts)) {
            $query->orderBy($sort, $direction === 'asc' ? 'asc' : 'desc');
        } else {
            $query->latest();
        }

        $products = $query->paginate($request->input('per_page', 20));

        // محاسبهٔ کارت‌های آماری
        $stats = [
            'total'        => $products->total(),
            'active'       => Product::where('tenant_id', $tenantId)->where('is_active', true)->count(),
            'inactive'     => Product::where('tenant_id', $tenantId)->where('is_active', false)->count(),
            'low_stock'    => Product::where('tenant_id', $tenantId)
                ->whereColumn('minimum_stock', '>', 'maximum_stock') // مثال ساده: موجودی صفر یا کمتر از حداقل
                ->count(), // می‌توانید دقیق‌تر کنید
        ];

        // درخواست AJAX
        if ($request->ajax() || $request->input('ajax')) {
            $tableHtml = view('warehouse.products._table', compact('products'))->render();
            return response()->json([
                'html'  => $tableHtml,
                'total' => $products->total(),
                'stats' => $stats,
            ]);
        }

        return view('warehouse.products.index', compact('products', 'categories', 'brands', 'stats'));
    }

    public function create()
    {
        Gate::authorize('access', 'products.create');

        $categories       = Category::where('tenant_id', $this->manager->getTenantId())->get();
        $brands           = Brand::where('tenant_id', $this->manager->getTenantId())->get();
        $measurementUnits = MeasurementUnit::where('tenant_id', $this->manager->getTenantId())->get();
        $attributes       = ProductAttribute::where('tenant_id', $this->manager->getTenantId())->get();
        $productTypes     = ProductType::where('tenant_id', $this->manager->getTenantId())->where('is_active', true)->get(); // اضافه

        return view('warehouse.products.create', compact('productTypes', 'categories', 'brands', 'measurementUnits', 'attributes'));
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
        $productTypes     = ProductType::where('tenant_id', $this->manager->getTenantId())->where('is_active', true)->get(); // اضافه

        $product->load('measurementUnits', 'attributeValues');

        return view('warehouse.products.edit', compact('productTypes','product', 'categories', 'brands', 'measurementUnits', 'attributes'));
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
