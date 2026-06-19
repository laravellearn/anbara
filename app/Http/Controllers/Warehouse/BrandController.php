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

        try {
            $data = $request->validate([
                'title'       => 'required|string|max:255',
                'description' => 'nullable|string',
                'is_active'   => 'boolean',
            ]);

            $data['tenant_id'] = $this->manager->getTenantId();

            Brand::create($data);

            return redirect()->route('warehouse.brands.index')->with('toast', [
                'message' => 'برند با موفقیت ایجاد شد.',
                'type'    => 'success',
                'title'   => 'ایجاد برند'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('show_create_modal', true);
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'خطا در ایجاد برند: ' . $e->getMessage()])
                ->withInput()
                ->with('show_create_modal', true);
        }
    }

    public function update(Request $request, Brand $brand)
    {
        Gate::authorize('access', 'brands.edit');

        try {
            $data = $request->validate([
                'title'       => 'required|string|max:255',
                'description' => 'nullable|string',
                'is_active'   => 'boolean',
            ]);

            $brand->update($data);

            return redirect()->route('warehouse.brands.index')->with('toast', [
                'message' => 'برند با موفقیت ویرایش شد.',
                'type'    => 'success',
                'title'   => 'ویرایش برند'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('show_edit_modal', true);
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'خطا در ویرایش برند: ' . $e->getMessage()])
                ->withInput()
                ->with('show_edit_modal', true);
        }
    }

    public function destroy(Brand $brand)
    {
        Gate::authorize('access', 'brands.delete');

        try {
            $brand->delete();

            return redirect()->route('warehouse.brands.index')->with('toast', [
                'message' => 'برند با موفقیت حذف شد.',
                'type'    => 'success',
                'title'   => 'حذف برند'
            ]);
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'خطا در حذف برند: ' . $e->getMessage()]);
        }
    }
}