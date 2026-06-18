<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\ProductAttribute;
use App\Services\TenantManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ProductAttributeController extends Controller
{
    protected $manager;

    public function __construct(TenantManager $manager)
    {
        $this->manager = $manager;
    }

    public function index()
    {
        Gate::authorize('access', 'product-attributes.view');

        $attributes = ProductAttribute::where('tenant_id', $this->manager->getTenantId())
            ->latest()
            ->paginate(20);

        return view('warehouse.product-attributes.index', compact('attributes'));
    }

    public function store(Request $request)
    {
        Gate::authorize('access', 'product-attributes.create');

        $data = $request->validate([
            'name'    => 'required|string|max:255',
            'type'    => 'required|in:text,number,select',
            'options' => 'nullable|json',
        ]);

        $data['tenant_id'] = $this->manager->getTenantId();
        ProductAttribute::create($data);

        flash()->success('ویژگی جدید ایجاد شد.');
        return redirect()->route('warehouse.product-attributes.index');
    }

    public function update(Request $request, ProductAttribute $attribute)
    {
        Gate::authorize('access', 'product-attributes.edit');

        $attribute->update($request->validate([
            'name'    => 'required|string|max:255',
            'type'    => 'required|in:text,number,select',
            'options' => 'nullable|json',
        ]));

        flash()->success('ویژگی ویرایش شد.');
        return redirect()->route('warehouse.product-attributes.index');
    }

    public function destroy(ProductAttribute $attribute)
    {
        Gate::authorize('access', 'product-attributes.delete');

        $attribute->delete();
        flash()->success('ویژگی حذف شد.');
        return back();
    }
}