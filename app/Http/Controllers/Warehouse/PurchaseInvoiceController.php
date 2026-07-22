<?php

namespace App\Http\Controllers\Warehouse;

use App\Models\Contact;
use App\Models\CostCenter;
use App\Models\FiscalYear;
use App\Models\MeasurementUnit;
use App\Models\Product;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseOrder;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class PurchaseInvoiceController extends BaseController
{
    public function __construct(\App\Services\TenantManager $manager)
    {
        parent::__construct($manager);
    }

    public function index(Request $request)
    {
        Gate::authorize('access', 'purchase-invoices.view');
        [$tenantId, $companyId] = $this->ctx();

        $query = PurchaseInvoice::with(['supplier', 'purchaseOrder', 'creator'])
            ->forTenant($tenantId, $companyId)
            ->withCount('items')
            ->latest('invoice_date');

        if ($request->filled('status'))     { $query->where('status', $request->status); }
        if ($request->filled('supplier_id')){ $query->where('supplier_id', $request->supplier_id); }
        if ($request->filled('date_from'))  { $query->whereDate('invoice_date', '>=', $request->date_from); }
        if ($request->filled('date_to'))    { $query->whereDate('invoice_date', '<=', $request->date_to); }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q
                ->where('invoice_number', 'like', "%$s%")
                ->orWhere('supplier_invoice_number', 'like', "%$s%")
            );
        }

        $invoices  = $query->paginate($request->per_page ?? 20)->withQueryString();
        $suppliers = Contact::where('tenant_id', $tenantId)->orderBy('name')->get();

        $stats = [
            'total'      => PurchaseInvoice::forTenant($tenantId, $companyId)->count(),
            'draft'      => PurchaseInvoice::forTenant($tenantId, $companyId)->where('status', 'draft')->count(),
            'registered' => PurchaseInvoice::forTenant($tenantId, $companyId)->where('status', 'registered')->count(),
            'unpaid'     => PurchaseInvoice::forTenant($tenantId, $companyId)->whereNotIn('status', ['paid', 'cancelled'])->count(),
        ];

        return view('warehouse.purchase-invoices.index', compact('invoices', 'suppliers', 'stats'));
    }

    public function create(Request $request)
    {
        Gate::authorize('access', 'purchase-invoices.create');
        [$warehouses, $products, $units, $costCenters, $fiscalYears, $suppliers, $purchaseOrders] = $this->formData();
        $purchaseInvoice = new PurchaseInvoice();
        $selectedPo = null;
        if ($request->filled('po_id')) {
            $selectedPo = PurchaseOrder::with('items.product', 'items.measurementUnit')
                ->where('tenant_id', $this->manager->getTenantId())
                ->findOrFail($request->po_id);
        }
        return view('warehouse.purchase-invoices.create', compact(
            'products', 'units', 'costCenters', 'fiscalYears', 'suppliers', 'purchaseOrders', 'purchaseInvoice', 'selectedPo'
        ));
    }

    public function store(Request $request)
    {
        Gate::authorize('access', 'purchase-invoices.create');
        $data = $request->validate([
            'supplier_id'              => 'nullable|exists:contacts,id',
            'supplier_invoice_number'  => 'nullable|string|max:100',
            'purchase_order_id'        => 'nullable|exists:purchase_orders,id',
            'fiscal_year_id'           => 'nullable|exists:fiscal_years,id',
            'cost_center_id'           => 'nullable|exists:cost_centers,id',
            'invoice_date'             => 'required|date',
            'due_date'                 => 'nullable|date|after_or_equal:invoice_date',
            'discount_percent'         => 'nullable|numeric|min:0|max:100',
            'tax_percent'              => 'nullable|numeric|min:0|max:100',
            'shipping_cost'            => 'nullable|numeric|min:0',
            'notes'                    => 'nullable|string|max:1000',
            'items'                    => 'required|array|min:1',
            'items.*.product_id'       => 'required|exists:products,id',
            'items.*.quantity'         => 'required|numeric|min:0.001',
            'items.*.unit_price'       => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($data) {
            $tenantId  = $this->manager->getTenantId();
            $companyId = $this->manager->getCompanyId();

            $invoice = PurchaseInvoice::create([
                ...$data,
                'tenant_id'      => $tenantId,
                'company_id'     => $companyId,
                'invoice_number' => $this->generateNumber($tenantId),
                'status'         => PurchaseInvoice::STATUS_DRAFT,
                'created_by'     => auth()->id(),
            ]);

            foreach ($data['items'] as $idx => $item) {
                $invoice->items()->create([
                    'product_id'             => $item['product_id'],
                    'purchase_order_item_id' => $item['purchase_order_item_id'] ?? null,
                    'measurement_unit_id'    => $item['measurement_unit_id'] ?? null,
                    'quantity'               => $item['quantity'],
                    'unit_price'             => $item['unit_price'],
                    'discount_percent'       => $item['discount_percent'] ?? 0,
                    'description'            => $item['description'] ?? null,
                    'sort_order'             => $idx,
                ]);
            }
        });

        return redirect()->route('warehouse.purchase-invoices.index')
            ->with('success', 'فاکتور خرید با موفقیت ثبت شد.');
    }

    public function show(PurchaseInvoice $purchaseInvoice)
    {
        Gate::authorize('access', 'purchase-invoices.view');
        $this->authorizeTenant($purchaseInvoice);
        $purchaseInvoice->load(['items.product', 'items.measurementUnit', 'supplier', 'purchaseOrder', 'creator', 'registeredBy', 'fiscalYear', 'costCenter']);
        return view('warehouse.purchase-invoices.show', compact('purchaseInvoice'));
    }

    public function edit(PurchaseInvoice $purchaseInvoice)
    {
        Gate::authorize('access', 'purchase-invoices.edit');
        $this->authorizeTenant($purchaseInvoice);
        abort_unless($purchaseInvoice->isEditable(), 403, 'این فاکتور قابل ویرایش نیست.');
        $purchaseInvoice->load('items');
        [$warehouses, $products, $units, $costCenters, $fiscalYears, $suppliers, $purchaseOrders] = $this->formData();
        $selectedPo = null;
        return view('warehouse.purchase-invoices.edit', compact(
            'purchaseInvoice', 'products', 'units', 'costCenters', 'fiscalYears', 'suppliers', 'purchaseOrders', 'selectedPo'
        ));
    }

    public function update(Request $request, PurchaseInvoice $purchaseInvoice)
    {
        Gate::authorize('access', 'purchase-invoices.edit');
        $this->authorizeTenant($purchaseInvoice);
        abort_unless($purchaseInvoice->isEditable(), 403);

        $data = $request->validate([
            'supplier_id'          => 'nullable|exists:contacts,id',
            'supplier_invoice_number' => 'nullable|string|max:100',
            'purchase_order_id'    => 'nullable|exists:purchase_orders,id',
            'fiscal_year_id'       => 'nullable|exists:fiscal_years,id',
            'cost_center_id'       => 'nullable|exists:cost_centers,id',
            'invoice_date'         => 'required|date',
            'due_date'             => 'nullable|date|after_or_equal:invoice_date',
            'discount_percent'     => 'nullable|numeric|min:0|max:100',
            'tax_percent'          => 'nullable|numeric|min:0|max:100',
            'shipping_cost'        => 'nullable|numeric|min:0',
            'notes'                => 'nullable|string|max:1000',
            'items'                => 'required|array|min:1',
            'items.*.product_id'   => 'required|exists:products,id',
            'items.*.quantity'     => 'required|numeric|min:0.001',
            'items.*.unit_price'   => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($purchaseInvoice, $data) {
            $purchaseInvoice->update($data);
            $purchaseInvoice->items()->delete();
            foreach ($data['items'] as $idx => $item) {
                $purchaseInvoice->items()->create([
                    'product_id'             => $item['product_id'],
                    'purchase_order_item_id' => $item['purchase_order_item_id'] ?? null,
                    'measurement_unit_id'    => $item['measurement_unit_id'] ?? null,
                    'quantity'               => $item['quantity'],
                    'unit_price'             => $item['unit_price'],
                    'discount_percent'       => $item['discount_percent'] ?? 0,
                    'description'            => $item['description'] ?? null,
                    'sort_order'             => $idx,
                ]);
            }
        });

        return redirect()->route('warehouse.purchase-invoices.show', $purchaseInvoice)
            ->with('success', 'فاکتور خرید ویرایش شد.');
    }

    public function destroy(PurchaseInvoice $purchaseInvoice)
    {
        Gate::authorize('access', 'purchase-invoices.delete');
        $this->authorizeTenant($purchaseInvoice);
        abort_unless($purchaseInvoice->isEditable(), 403);
        $purchaseInvoice->delete();
        return redirect()->route('warehouse.purchase-invoices.index')->with('success', 'فاکتور حذف شد.');
    }

    public function register(PurchaseInvoice $purchaseInvoice)
    {
        Gate::authorize('access', 'purchase-invoices.approve');
        $this->authorizeTenant($purchaseInvoice);
        abort_unless($purchaseInvoice->canRegister(), 403);
        $purchaseInvoice->update([
            'status'        => PurchaseInvoice::STATUS_REGISTERED,
            'registered_by' => auth()->id(),
            'registered_at' => now(),
        ]);
        // ارسال فاکتور به ایمیل تأمین‌کننده
        try {
            $supplier = $purchaseInvoice->supplier;
            if ($supplier && $supplier->email) {
                \Illuminate\Support\Facades\Mail::to($supplier->email)
                    ->send(new \App\Mail\PurchaseInvoiceMail($purchaseInvoice));
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('خطا در ارسال ایمیل فاکتور: ' . $e->getMessage());
        }
        // ارسال webhook outbound
        try {
            app(\App\Services\WebhookDispatcher::class)->dispatch(
                'invoice.registered',
                [
                    'invoice_id'     => $purchaseInvoice->id,
                    'invoice_number' => $purchaseInvoice->invoice_number,
                    'supplier'       => $purchaseInvoice->supplier?->name,
                    'status'         => 'registered',
                ],
                $purchaseInvoice->tenant_id
            );
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('خطا در ارسال webhook فاکتور: ' . $e->getMessage());
        }
        return back()->with('success', 'فاکتور ثبت رسمی شد و به ایمیل تأمین‌کننده ارسال گردید.');
    }

    public function markPaid(Request $request, PurchaseInvoice $purchaseInvoice)
    {
        Gate::authorize('access', 'purchase-invoices.pay');
        $this->authorizeTenant($purchaseInvoice);
        abort_unless($purchaseInvoice->canPay(), 403);
        $request->validate([
            'payment_method'    => 'required|string|max:50',
            'payment_reference' => 'nullable|string|max:100',
            'payment_date'      => 'required|date',
        ]);
        $purchaseInvoice->update([
            'status'             => PurchaseInvoice::STATUS_PAID,
            'payment_method'     => $request->payment_method,
            'payment_reference'  => $request->payment_reference,
            'payment_date'       => $request->payment_date,
        ]);
        return back()->with('success', 'پرداخت فاکتور ثبت شد.');
    }

    public function cancel(Request $request, PurchaseInvoice $purchaseInvoice)
    {
        Gate::authorize('access', 'purchase-invoices.approve');
        $this->authorizeTenant($purchaseInvoice);
        abort_unless($purchaseInvoice->canCancel(), 403);
        $request->validate(['cancellation_reason' => 'required|string|max:500']);
        $purchaseInvoice->update([
            'status'               => PurchaseInvoice::STATUS_CANCELLED,
            'cancellation_reason'  => $request->cancellation_reason,
        ]);
        return back()->with('success', 'فاکتور لغو شد.');
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────
    private function ctx(): array
    {
        return [$this->manager->getTenantId(), $this->manager->getCompanyId()];
    }

    private function authorizeTenant(PurchaseInvoice $inv): void
    {
        abort_unless($inv->tenant_id === $this->manager->getTenantId(), 403);
    }

    private function generateNumber(int $tenantId): string
    {
        $count = PurchaseInvoice::where('tenant_id', $tenantId)->withTrashed()->count() + 1;
        return 'INV-' . now()->format('Ym') . '-' . str_pad($count, 5, '0', STR_PAD_LEFT);
    }

    private function formData(): array
    {
        $tid = $this->manager->getTenantId();
        return [
            Warehouse::where('tenant_id', $tid)->where('is_active', true)->orderBy('title')->get(),
            Product::where('tenant_id', $tid)->where('is_active', true)->orderBy('title')->get(),
            MeasurementUnit::where('tenant_id', $tid)->where('is_active', true)->orderBy('title')->get(),
            CostCenter::where('tenant_id', $tid)->where('is_active', true)->orderBy('title')->get(),
            FiscalYear::where('tenant_id', $tid)->orderBy('start_date', 'desc')->get(),
            Contact::where('tenant_id', $tid)->orderBy('name')->get(),
            PurchaseOrder::where('tenant_id', $tid)
                ->whereIn('status', ['received', 'partial_received', 'sent', 'confirmed'])
                ->orderBy('po_number')->get(),
        ];
    }
}
