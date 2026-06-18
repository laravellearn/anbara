<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\ProductBarcode;
use App\Models\Product;
use App\Services\TenantManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ProductBarcodeController extends Controller
{
    protected $manager;

    public function __construct(TenantManager $manager)
    {
        $this->manager = $manager;
    }

    public function index()
    {
        Gate::authorize('access', 'barcodes.view');

        $barcodes = ProductBarcode::where('tenant_id', $this->manager->getTenantId())
            ->with('product')
            ->latest()
            ->paginate(20);

        $products = Product::where('tenant_id', $this->manager->getTenantId())->get();

        return view('warehouse.product-barcodes.index', compact('barcodes', 'products'));
    }

    public function store(Request $request)
    {
        Gate::authorize('access', 'barcodes.create');

        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'barcode'    => 'required|string|unique:product_barcodes,barcode',
            'is_default' => 'boolean',
        ]);

        $data['tenant_id'] = $this->manager->getTenantId();
        ProductBarcode::create($data);

        flash()->success('بارکد جدید ثبت شد.');
        return redirect()->route('warehouse.product-barcodes.index');
    }

    public function update(Request $request, ProductBarcode $barcode)
    {
        Gate::authorize('access', 'barcodes.edit');

        $barcode->update($request->validate([
            'product_id' => 'required|exists:products,id',
            'barcode'    => 'required|string|unique:product_barcodes,barcode,' . $barcode->id,
            'is_default' => 'boolean',
        ]));

        flash()->success('بارکد ویرایش شد.');
        return redirect()->route('warehouse.product-barcodes.index');
    }

    public function destroy(ProductBarcode $barcode)
    {
        Gate::authorize('access', 'barcodes.delete');

        $barcode->delete();
        flash()->success('بارکد حذف شد.');
        return back();
    }
}