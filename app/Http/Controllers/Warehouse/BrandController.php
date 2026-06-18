<?php

namespace App\Http\Controllers\Warehouse;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class BrandController extends BaseController
{
    public function index(Request $request)
    {
        Gate::authorize('access', 'brands.view');
        $tenantId = $this->manager->getTenantId();

        $query = Brand::where('tenant_id', $tenantId);
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                    ->orWhere('description', 'like', "%{$request->search}%");
            });
        }
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }
        $query->latest();
        $brands = $query->paginate($request->per_page ?? 20);

        $stats = [
            'total'    => Brand::where('tenant_id', $tenantId)->count(),
            'active'   => Brand::where('tenant_id', $tenantId)->where('is_active', true)->count(),
            'inactive' => Brand::where('tenant_id', $tenantId)->where('is_active', false)->count(),
        ];

        if ($request->ajax() || $request->input('ajax')) {
            return response()->json([
                'html'      => view('warehouse.brands._table', compact('brands'))->render(),
                'statsHtml' => view('warehouse.brands._stats', compact('stats'))->render(),
                'total'     => $brands->total(),
            ]);
        }

        return view('warehouse.brands.index', compact('brands', 'stats'));
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
