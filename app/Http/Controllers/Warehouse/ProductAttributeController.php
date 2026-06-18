<?php

namespace App\Http\Controllers\Warehouse;

use App\Models\ProductAttribute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ProductAttributeController extends BaseController
{
    public function index(Request $request)
    {
        Gate::authorize('access', 'product-attributes.view');
        $tenantId = $this->manager->getTenantId();

        $query = ProductAttribute::where('tenant_id', $tenantId);
        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }
        $query->latest();
        $attributes = $query->paginate($request->per_page ?? 20);

        $stats = [
            'total'    => ProductAttribute::where('tenant_id', $tenantId)->count(),
            'active'   => ProductAttribute::where('tenant_id', $tenantId)->where('is_active', true)->count(),
            'inactive' => ProductAttribute::where('tenant_id', $tenantId)->where('is_active', false)->count(),
        ];

        if ($request->ajax() || $request->input('ajax')) {
            return response()->json([
                'html'      => view('warehouse.product-attributes._table', compact('attributes'))->render(),
                'statsHtml' => view('warehouse.product-attributes._stats', compact('stats'))->render(),
                'total'     => $attributes->total(),
            ]);
        }

        return view('warehouse.product-attributes.index', compact('attributes', 'stats'));
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
