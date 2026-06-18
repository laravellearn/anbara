<?php

namespace App\Http\Controllers\Warehouse;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class BrandController extends BaseController
{
    public function index()
    {
        Gate::authorize('access', 'brands.view');

        $brands = Brand::where('tenant_id', $this->manager->getTenantId())
            ->latest()
            ->paginate(20);

        return view('warehouse.brands.index', compact('brands'));
    }

    public function store(Request $request)
    {
        Gate::authorize('access', 'brands.create');

        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active'   => 'boolean',
        ]);

        $data['tenant_id'] = $this->manager->getTenantId();

        Brand::create($data);

        flash()->success('برند ایجاد شد.');
        return redirect()->route('warehouse.brands.index');
    }

    public function update(Request $request, Brand $brand)
    {
        Gate::authorize('access', 'brands.edit');

        $brand->update($request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active'   => 'boolean',
        ]));

        flash()->success('برند ویرایش شد.');
        return redirect()->route('warehouse.brands.index');
    }

    public function destroy(Brand $brand)
    {
        Gate::authorize('access', 'brands.delete');

        $brand->delete();

        flash()->success('برند حذف شد.');
        return back();
    }
}