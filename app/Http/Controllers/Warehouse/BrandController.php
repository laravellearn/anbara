<?php

namespace App\Http\Controllers\Warehouse;

use App\Models\Brand;
use App\Http\Requests\Warehouse\StoreBrandRequest;
use App\Http\Requests\Warehouse\UpdateBrandRequest;
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
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $brands = $query->latest()->paginate($request->per_page ?? 20);

        $stats = [
            'total'    => Brand::where('tenant_id', $tenantId)->count(),
            'active'   => Brand::where('tenant_id', $tenantId)->where('is_active', true)->count(),
            'inactive' => Brand::where('tenant_id', $tenantId)->where('is_active', false)->count(),
        ];

        if ($request->ajax() || $request->input('ajax')) {
            return response()->json([
                'html'  => view('warehouse.brands._table', compact('brands'))->render(),
                'total' => $brands->total(),
            ]);
        }

        return view('warehouse.brands.index', compact('brands', 'stats'));
    }

    public function store(StoreBrandRequest $request)
    {
        Gate::authorize('access', 'brands.create');

        try {
            $data = $request->validated();
            $data['tenant_id']  = $this->manager->getTenantId();
            $data['company_id'] = $this->manager->getCompanyId();

            Brand::create($data);

            return redirect()->route('warehouse.brands.index')->with('toast', [
                'message' => 'برند با موفقیت ایجاد شد.',
                'type'    => 'success',
                'title'   => 'ایجاد برند',
            ]);
        } catch (\Exception $e) {
            \Log::error($e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            return redirect()->back()
                ->withErrors(['error' => 'خطا در ایجاد برند'])
                ->withInput();
        }
    }

    public function update(UpdateBrandRequest $request, Brand $brand)
    {
        Gate::authorize('access', 'brands.edit');
        $this->authorizeBrand($brand);

        try {
            $brand->update($request->validated());

            return redirect()->route('warehouse.brands.index')->with('toast', [
                'message' => 'برند با موفقیت ویرایش شد.',
                'type'    => 'success',
                'title'   => 'ویرایش برند',
            ]);
        } catch (\Exception $e) {
            \Log::error($e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            return redirect()->back()
                ->withErrors(['error' => 'خطا در ویرایش برند'])
                ->withInput();
        }
    }

    public function destroy(Brand $brand)
    {
        Gate::authorize('access', 'brands.delete');
        $this->authorizeBrand($brand);

        try {
            $brand->delete();

            return redirect()->route('warehouse.brands.index')->with('toast', [
                'message' => 'برند با موفقیت حذف شد.',
                'type'    => 'success',
                'title'   => 'حذف برند',
            ]);
        } catch (\Exception $e) {
            \Log::error($e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            return redirect()->back()
                ->withErrors(['error' => 'خطا در حذف برند']);
        }
    }

    private function authorizeBrand(Brand $brand): void
    {
        if ($brand->tenant_id !== $this->manager->getTenantId()) {
            abort(403);
        }
    }
}
