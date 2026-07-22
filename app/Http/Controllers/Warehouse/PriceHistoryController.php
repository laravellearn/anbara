<?php

namespace App\Http\Controllers\Warehouse;

use App\Models\Contact;
use App\Models\PriceHistory;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/**
 * تاریخچه قیمت کالا به تفکیک تأمین‌کننده
 */
class PriceHistoryController extends BaseController
{
    public function index(Request $request)
    {
        Gate::authorize('access', 'price-history.view');

        [$tenantId, $companyId] = [$this->manager->getTenantId(), $this->manager->getCompanyId()];

        $products   = Product::where('tenant_id', $tenantId)->where('is_active', true)->orderBy('title')->get();
        $suppliers  = Contact::where('tenant_id', $tenantId)->where('is_supplier', true)->orderBy('full_name')->get();

        $rows = collect();
        $product  = null;
        $supplier = null;

        if ($request->filled('product_id')) {
            $product = Product::where('tenant_id', $tenantId)->findOrFail($request->product_id);

            $query = PriceHistory::with(['supplier', 'product', 'recorder'])
                ->where('tenant_id', $tenantId)
                ->where('company_id', $companyId)
                ->where('product_id', $request->product_id)
                ->orderByDesc('price_date');

            if ($request->filled('supplier_id')) {
                $supplier = Contact::where('tenant_id', $tenantId)->findOrFail($request->supplier_id);
                $query->where('supplier_id', $request->supplier_id);
            }

            $rows = $query->paginate(30)->withQueryString();
        }

        return view('warehouse.price-history.index', compact(
            'rows', 'products', 'suppliers', 'product', 'supplier'
        ));
    }

    /** ثبت قیمت جدید */
    public function store(Request $request)
    {
        Gate::authorize('access', 'price-history.create');

        $request->validate([
            'product_id'   => ['required', 'integer'],
            'supplier_id'  => ['required', 'integer'],
            'unit_price'   => ['required', 'numeric', 'min:0'],
            'currency'     => ['required', 'string', 'max:10'],
            'price_date'   => ['required', 'date'],
            'notes'        => ['nullable', 'string', 'max:500'],
        ]);

        $tenantId  = $this->manager->getTenantId();
        $companyId = $this->manager->getCompanyId();

        PriceHistory::create([
            'tenant_id'   => $tenantId,
            'company_id'  => $companyId,
            'product_id'  => $request->product_id,
            'supplier_id' => $request->supplier_id,
            'unit_price'  => $request->unit_price,
            'currency'    => $request->currency,
            'price_date'  => $request->price_date,
            'source'      => $request->source ?? 'manual',
            'notes'       => $request->notes,
            'recorded_by' => auth()->id(),
        ]);

        return back()->with('toast', ['type' => 'success', 'message' => 'قیمت جدید ثبت شد.']);
    }

    /** حذف رکورد قیمت */
    public function destroy(PriceHistory $priceHistory)
    {
        Gate::authorize('access', 'price-history.delete');
        if ($priceHistory->tenant_id !== $this->manager->getTenantId()) abort(403);

        $priceHistory->delete();
        return back()->with('toast', ['type' => 'success', 'message' => 'رکورد قیمت حذف شد.']);
    }

    /** API: آخرین قیمت کالا از تأمین‌کننده (برای فرم PO) */
    public function lastPrice(Request $request)
    {
        Gate::authorize('access', 'price-history.view');

        $tenantId = $this->manager->getTenantId();

        $last = PriceHistory::where('tenant_id', $tenantId)
            ->where('product_id',  $request->product_id)
            ->where('supplier_id', $request->supplier_id)
            ->orderByDesc('price_date')
            ->first();

        return response()->json([
            'found'      => (bool)$last,
            'unit_price' => $last?->unit_price,
            'currency'   => $last?->currency,
            'price_date' => $last?->price_date?->format('Y-m-d'),
        ]);
    }
}
