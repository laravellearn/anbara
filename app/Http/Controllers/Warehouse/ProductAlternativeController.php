<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\ProductAlternative;
use App\Models\Product;
use App\Services\TenantManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ProductAlternativeController extends Controller
{
    protected $manager;

    public function __construct(TenantManager $manager)
    {
        $this->manager = $manager;
    }

    public function index()
    {
        Gate::authorize('access', 'product-alternatives.view');

        $alternatives = ProductAlternative::where('tenant_id', $this->manager->getTenantId())
            ->with(['product', 'alternativeProduct'])
            ->latest()
            ->paginate(20);

        $products = Product::where('tenant_id', $this->manager->getTenantId())->get();

        return view('warehouse.product-alternatives.index', compact('alternatives', 'products'));
    }

    public function store(Request $request)
    {
        Gate::authorize('access', 'product-alternatives.create');

        $data = $request->validate([
            'product_id'            => 'required|exists:products,id',
            'alternative_product_id'=> 'required|exists:products,id|different:product_id',
        ]);

        $data['tenant_id'] = $this->manager->getTenantId();
        ProductAlternative::create($data);

        flash()->success('کالای جایگزین ثبت شد.');
        return redirect()->route('warehouse.product-alternatives.index');
    }

    public function update(Request $request, ProductAlternative $alternative)
    {
        Gate::authorize('access', 'product-alternatives.edit');

        $alternative->update($request->validate([
            'product_id'            => 'required|exists:products,id',
            'alternative_product_id'=> 'required|exists:products,id|different:product_id',
        ]));

        flash()->success('جایگزین ویرایش شد.');
        return redirect()->route('warehouse.product-alternatives.index');
    }

    public function destroy(ProductAlternative $alternative)
    {
        Gate::authorize('access', 'product-alternatives.delete');

        $alternative->delete();
        flash()->success('جایگزین حذف شد.');
        return back();
    }
}