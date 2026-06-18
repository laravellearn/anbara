<?php

namespace App\Http\Controllers\Warehouse;

use App\Models\ProductAttribute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ProductAttributeController extends BaseController
{
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

        flash()->success('ویژگی ایجاد شد.');
        return redirect()->route('warehouse.product-attributes.index');
    }

    public function update(Request $request, ProductAttribute $productAttribute)
    {
        Gate::authorize('access', 'product-attributes.edit');

        $productAttribute->update($request->validate([
            'name'    => 'required|string|max:255',
            'type'    => 'required|in:text,number,select',
            'options' => 'nullable|json',
        ]));

        flash()->success('ویژگی ویرایش شد.');
        return redirect()->route('warehouse.product-attributes.index');
    }

    public function destroy(ProductAttribute $productAttribute)
    {
        Gate::authorize('access', 'product-attributes.delete');

        $productAttribute->delete();

        flash()->success('ویژگی حذف شد.');
        return back();
    }
}