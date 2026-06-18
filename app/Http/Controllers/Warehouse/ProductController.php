<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Unit;
use App\Services\TenantManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ProductController extends Controller
{
    protected $manager;

    public function __construct(TenantManager $manager)
    {
        $this->manager = $manager;
    }

    public function index()
    {
        Gate::authorize('access', 'products.view');

        $products = Product::where('tenant_id', $this->manager->getTenantId())
            ->with(['category', 'unit', 'company'])
            ->latest()
            ->paginate(20);

        $categories = ProductCategory::where('tenant_id', $this->manager->getTenantId())->get();
        $units      = Unit::where('tenant_id', $this->manager->getTenantId())->get();

        return view('warehouse.products.index', compact('products', 'categories', 'units'));
    }

    public function store(Request $request)
    {
        Gate::authorize('access', 'products.create');

        $data = $request->validate([
            'category_id' => 'nullable|exists:product_categories,id',
            'unit_id'     => 'nullable|exists:units,id',
            'name'        => 'required|string|max:255',
            'sku'         => 'nullable|string|max:50',
            'barcode'     => 'nullable|string|unique:products,barcode',
            'description' => 'nullable|string',
            'min_stock'   => 'nullable|numeric|min:0',
            'max_stock'   => 'nullable|numeric|min:0',
            'is_active'   => 'boolean',
        ]);

        $data['tenant_id']  = $this->manager->getTenantId();
        $data['company_id'] = $this->manager->getCompanyId();

        Product::create($data);

        flash()->success('کالای جدید با موفقیت ایجاد شد.');
        return redirect()->route('warehouse.products.index');
    }

    public function update(Request $request, Product $product)
    {
        Gate::authorize('access', 'products.edit');

        $product->update($request->validate([
            'category_id' => 'nullable|exists:product_categories,id',
            'unit_id'     => 'nullable|exists:units,id',
            'name'        => 'required|string|max:255',
            'sku'         => 'nullable|string|max:50',
            'barcode'     => 'nullable|string|unique:products,barcode,' . $product->id,
            'description' => 'nullable|string',
            'min_stock'   => 'nullable|numeric|min:0',
            'max_stock'   => 'nullable|numeric|min:0',
            'is_active'   => 'boolean',
        ]));

        flash()->success('کالا ویرایش شد.');
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