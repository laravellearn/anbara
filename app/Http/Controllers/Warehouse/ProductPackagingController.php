<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\ProductPackaging;
use App\Models\Product;
use App\Models\Unit;
use App\Services\TenantManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ProductPackagingController extends Controller
{
    protected $manager;

    public function __construct(TenantManager $manager)
    {
        $this->manager = $manager;
    }

    public function index()
    {
        Gate::authorize('access', 'product-packaging.view');

        $packagings = ProductPackaging::where('tenant_id', $this->manager->getTenantId())
            ->with(['product', 'unit'])
            ->latest()
            ->paginate(20);

        $products = Product::where('tenant_id', $this->manager->getTenantId())->get();
        $units    = Unit::where('tenant_id', $this->manager->getTenantId())->get();

        return view('warehouse.packagings.index', compact('packagings', 'products', 'units'));
    }

    public function store(Request $request)
    {
        Gate::authorize('access', 'product-packaging.create');

        $data = $request->validate([
            'product_id'         => 'required|exists:products,id',
            'unit_id'            => 'nullable|exists:units,id',
            'name'               => 'required|string|max:255',
            'quantity_per_unit'  => 'required|numeric|min:0',
        ]);

        $data['tenant_id'] = $this->manager->getTenantId();
        ProductPackaging::create($data);

        flash()->success('بسته‌بندی جدید ثبت شد.');
        return redirect()->route('warehouse.product-packaging.index');
    }

    public function update(Request $request, ProductPackaging $packaging)
    {
        Gate::authorize('access', 'product-packaging.edit');

        $packaging->update($request->validate([
            'product_id'         => 'required|exists:products,id',
            'unit_id'            => 'nullable|exists:units,id',
            'name'               => 'required|string|max:255',
            'quantity_per_unit'  => 'required|numeric|min:0',
        ]));

        flash()->success('بسته‌بندی ویرایش شد.');
        return redirect()->route('warehouse.product-packaging.index');
    }

    public function destroy(ProductPackaging $packaging)
    {
        Gate::authorize('access', 'product-packaging.delete');

        $packaging->delete();
        flash()->success('بسته‌بندی حذف شد.');
        return back();
    }
}