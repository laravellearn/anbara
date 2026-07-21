<?php

namespace App\Http\Controllers\Warehouse;

use App\Models\Product;
use App\Models\SerialBatch;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class SerialBatchController extends BaseController
{
    public function __construct(\App\Services\TenantManager $manager)
    {
        parent::__construct($manager);
    }

    public function index(Request $request)
    {
        Gate::authorize('access', 'serial-batch.view');
        [$tenantId, $companyId] = $this->ctx();

        $query = SerialBatch::with(['product', 'warehouse'])
            ->where('tenant_id', $tenantId)
            ->where('company_id', $companyId)
            ->latest();

        if ($request->filled('product_id'))  $query->where('product_id', $request->product_id);
        if ($request->filled('warehouse_id'))$query->where('warehouse_id', $request->warehouse_id);
        if ($request->filled('status'))      $query->where('status', $request->status);
        if ($request->filled('type'))        $query->where('tracking_type', $request->type);
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('serial_number', 'like', "%$s%")->orWhere('batch_number', 'like', "%$s%"));
        }
        if ($request->filled('expiring_days')) {
            $query->expiringSoon((int)$request->expiring_days);
        }

        $items      = $query->paginate(25)->withQueryString();
        $products   = Product::where('tenant_id', $tenantId)->where('is_active', true)->orderBy('title')->get();
        $warehouses = Warehouse::where('tenant_id', $tenantId)->where('is_active', true)->orderBy('title')->get();

        $stats = [
            'total'          => SerialBatch::where('tenant_id', $tenantId)->count(),
            'in_stock'       => SerialBatch::where('tenant_id', $tenantId)->where('status', 'in_stock')->count(),
            'expiring_soon'  => SerialBatch::where('tenant_id', $tenantId)->expiringSoon(30)->count(),
            'expired'        => SerialBatch::where('tenant_id', $tenantId)->expired()->count(),
        ];

        return view('warehouse.serial-batch.index', compact('items', 'products', 'warehouses', 'stats'));
    }

    public function store(Request $request)
    {
        Gate::authorize('access', 'serial-batch.create');
        [$tenantId, $companyId] = $this->ctx();

        $data = $request->validate([
            'product_id'      => 'required|exists:products,id',
            'warehouse_id'    => 'nullable|exists:warehouses,id',
            'tracking_type'   => 'required|in:serial,batch',
            'serial_number'   => 'required_if:tracking_type,serial|nullable|string|max:100',
            'batch_number'    => 'required_if:tracking_type,batch|nullable|string|max:100',
            'manufacture_date'=> 'nullable|date',
            'expiry_date'     => 'nullable|date',
            'quantity'        => 'required|numeric|min:0.001',
            'notes'           => 'nullable|string|max:500',
        ]);

        SerialBatch::create(array_merge($data, [
            'tenant_id'  => $tenantId,
            'company_id' => $companyId,
            'status'     => 'in_stock',
            'created_by' => auth()->id(),
        ]));

        return back()->with('success', 'سریال/بچ با موفقیت ثبت شد.');
    }

    public function update(Request $request, SerialBatch $serialBatch)
    {
        Gate::authorize('access', 'serial-batch.edit');
        abort_unless($serialBatch->tenant_id === auth()->user()->tenant_id, 403);
        $request->validate(['status' => 'required|in:in_stock,issued,returned,scrapped', 'notes' => 'nullable|string|max:500']);
        $serialBatch->update($request->only('status', 'notes'));
        return back()->with('success', 'وضعیت بروزرسانی شد.');
    }

    /** گزارش ردیابی یک محصول */
    public function productLedger(Request $request, Product $product)
    {
        Gate::authorize('access', 'serial-batch.view');
        [$tenantId] = $this->ctx();
        abort_unless($product->tenant_id === $tenantId, 403);

        $items = SerialBatch::with('warehouse', 'warehouseDocument')
            ->where('tenant_id', $tenantId)
            ->where('product_id', $product->id)
            ->orderByDesc('created_at')
            ->paginate(30);

        return view('warehouse.serial-batch.ledger', compact('product', 'items'));
    }
}
