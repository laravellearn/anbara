<?php

namespace App\Http\Controllers\Warehouse;

use App\Models\Product;
use App\Services\TenantManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class BarcodeController extends BaseController
{
    public function __construct(TenantManager $manager)
    {
        parent::__construct($manager);
    }

    /**
     * صفحه چاپ بارکد / QR کالاها
     */
    public function index(Request $request)
    {
        Gate::authorize('access', 'products.view');
        [$tenantId] = [$this->manager->getTenantId()];

        $query = Product::where('tenant_id', $tenantId)->where('is_active', true);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q
                ->where('title', 'like', "%{$s}%")
                ->orWhere('sku', 'like', "%{$s}%")
            );
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $products   = $query->with('category')->paginate(30);
        $categories = \App\Models\Category::where('tenant_id', $tenantId)->get();

        // کالاهای انتخاب‌شده برای چاپ (از session یا پارامتر GET)
        $selected = collect(explode(',', $request->input('ids', '')))->filter()->map(fn($v) => (int)$v);

        return view('warehouse.barcode.index', compact('products', 'categories', 'selected'));
    }

    /**
     * صفحه چاپ بارکدهای انتخاب‌شده
     */
    public function print(Request $request)
    {
        Gate::authorize('access', 'products.view');
        $tenantId = $this->manager->getTenantId();

        $ids      = array_filter(array_map('intval', explode(',', $request->input('ids', ''))));
        $type     = in_array($request->input('type', 'barcode'), ['barcode', 'qr']) ? $request->input('type', 'barcode') : 'barcode';
        $copies   = max(1, min(10, (int)$request->input('copies', 1)));

        if (empty($ids)) {
            return redirect()->route('warehouse.barcode.index')->with('toast', [
                'message' => 'هیچ کالایی انتخاب نشده.', 'type' => 'warning', 'title' => 'چاپ بارکد',
            ]);
        }

        $products = Product::where('tenant_id', $tenantId)
            ->whereIn('id', $ids)
            ->get();

        return view('warehouse.barcode.print', compact('products', 'type', 'copies'));
    }

    /**
     * AJAX: جستجوی کالا با بارکد / QR
     */
    public function scan(Request $request)
    {
        $tenantId = $this->manager->getTenantId();
        $code     = trim($request->input('code', ''));

        if (!$code) {
            return response()->json(['error' => 'کد خوانده‌نشده.'], 422);
        }

        $product = Product::where('tenant_id', $tenantId)
            ->where(fn($q) => $q->where('sku', $code)->orWhere('barcode', $code))
            ->with('measurementUnit', 'category')
            ->first();

        if (!$product) {
            return response()->json(['error' => 'کالایی با این کد یافت نشد.'], 404);
        }

        return response()->json([
            'id'       => $product->id,
            'title'    => $product->title,
            'sku'      => $product->sku,
            'barcode'  => $product->barcode ?? $product->sku,
            'unit'     => $product->measurementUnit?->title,
            'category' => $product->category?->title,
            'price'    => $product->sale_price ?? 0,
            'stock'    => $product->currentStock(),
        ]);
    }
}
