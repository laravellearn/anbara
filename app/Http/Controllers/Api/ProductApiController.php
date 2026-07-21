<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\TenantManager;
use Illuminate\Http\Request;

class ProductApiController extends Controller
{
    public function __construct(private TenantManager $manager) {}

    public function index(Request $request)
    {
        $tenantId = $this->manager->getTenantId();

        $query = Product::where('tenant_id', $tenantId)->where('is_active', true)
            ->with(['category', 'measurementUnit']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('title', 'like', "%{$s}%")->orWhere('sku', 'like', "%{$s}%"));
        }
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $perPage = min(100, max(10, (int)$request->input('per_page', 20)));
        $products = $query->paginate($perPage);

        return response()->json([
            'data' => $products->map(fn($p) => [
                'id'           => $p->id,
                'title'        => $p->title,
                'sku'          => $p->sku,
                'barcode'      => $p->barcode ?? $p->sku,
                'category'     => $p->category?->title,
                'unit'         => $p->measurementUnit?->title,
                'purchase_price' => $p->purchase_price,
                'sale_price'   => $p->sale_price,
                'minimum_stock'=> $p->minimum_stock,
                'is_active'    => $p->is_active,
            ]),
            'meta' => [
                'total'        => $products->total(),
                'per_page'     => $products->perPage(),
                'current_page' => $products->currentPage(),
                'last_page'    => $products->lastPage(),
            ],
        ]);
    }

    public function show(int $id)
    {
        $tenantId = $this->manager->getTenantId();
        $product  = Product::where('tenant_id', $tenantId)->findOrFail($id);
        $product->load(['category', 'measurementUnit']);

        return response()->json([
            'id'            => $product->id,
            'title'         => $product->title,
            'sku'           => $product->sku,
            'barcode'       => $product->barcode ?? $product->sku,
            'category'      => $product->category?->title,
            'unit'          => $product->measurementUnit?->title,
            'purchase_price'=> $product->purchase_price,
            'sale_price'    => $product->sale_price,
            'minimum_stock' => $product->minimum_stock,
            'stock'         => $product->currentStock(),
            'is_active'     => $product->is_active,
        ]);
    }
}
