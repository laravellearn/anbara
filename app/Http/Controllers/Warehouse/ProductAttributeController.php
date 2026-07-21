<?php

namespace App\Http\Controllers\Warehouse;

use App\Models\ProductAttribute;
use App\Http\Requests\Warehouse\StoreProductAttributeRequest;
use App\Http\Requests\Warehouse\UpdateProductAttributeRequest;
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

    public function store(StoreProductAttributeRequest $request)
    {
        Gate::authorize('access', 'product-attributes.create');

        try {
            $data = $request->validated();

            // تبدیل رشتهٔ کاما-جدا (مثلاً "قرمز,آبی") به JSON آرایه
            if (!empty($data['options'])) {
                $optionsArray = array_filter(array_map('trim', explode(',', $data['options'])), fn($v) => $v !== '');
                $data['options'] = !empty($optionsArray) ? json_encode(array_values($optionsArray)) : null;
            } else {
                $data['options'] = null;
            }

            $data['tenant_id'] = $this->manager->getTenantId();
            $data['is_active'] = $request->boolean('is_active'); // تضمین true/false

            ProductAttribute::create($data);

            return redirect()->route('warehouse.product-attributes.index')->with('toast', [
                'message' => 'ویژگی با موفقیت ایجاد شد.',
                'type'    => 'success',
                'title'   => 'ایجاد ویژگی'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('show_create_modal', true);
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'خطا در ایجاد ویژگی: ' . $e->getMessage()])
                ->withInput()
                ->with('show_create_modal', true);
        }
    }

    public function update(UpdateProductAttributeRequest $request, ProductAttribute $productAttribute)
    {
        Gate::authorize('access', 'product-attributes.edit');

        try {
            $data = $request->validated();

            if (!empty($data['options'])) {
                $optionsArray = array_filter(array_map('trim', explode(',', $data['options'])), fn($v) => $v !== '');
                $data['options'] = !empty($optionsArray) ? json_encode(array_values($optionsArray)) : null;
            } else {
                $data['options'] = null;
            }

            $data['is_active'] = $request->boolean('is_active');

            $productAttribute->update($data);

            return redirect()->route('warehouse.product-attributes.index')->with('toast', [
                'message' => 'ویژگی با موفقیت ویرایش شد.',
                'type'    => 'success',
                'title'   => 'ویرایش ویژگی'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('show_edit_modal', true);
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'خطا در ویرایش ویژگی: ' . $e->getMessage()])
                ->withInput()
                ->with('show_edit_modal', true);
        }
    }

    public function destroy(ProductAttribute $productAttribute)
    {
        Gate::authorize('access', 'product-attributes.delete');

        try {
            $productAttribute->delete();

            return redirect()->route('warehouse.product-attributes.index')->with('toast', [
                'message' => 'ویژگی با موفقیت حذف شد.',
                'type'    => 'success',
                'title'   => 'حذف ویژگی'
            ]);
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'خطا در حذف ویژگی: ' . $e->getMessage()]);
        }
    }
}