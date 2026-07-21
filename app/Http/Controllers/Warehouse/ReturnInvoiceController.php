<?php

namespace App\Http\Controllers\Warehouse;

use App\Models\Contact;
use App\Models\FiscalYear;
use App\Models\MeasurementUnit;
use App\Models\Product;
use App\Models\ReturnInvoice;
use App\Models\SalesInvoice;
use App\Models\PurchaseInvoice;
use App\Models\Warehouse;
use App\Services\ReturnInvoiceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ReturnInvoiceController extends BaseController
{
    public function __construct(
        \App\Services\TenantManager $manager,
        private ReturnInvoiceService $service
    ) {
        parent::__construct($manager);
    }

    // ─── لیست ────────────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        Gate::authorize('access', 'return-invoices.view');
        [$tenantId, $companyId] = $this->ctx();

        $query = ReturnInvoice::with(['contact', 'warehouse', 'creator'])
            ->forTenant($tenantId, $companyId)
            ->latest('return_date');

        $activeFY = $this->manager->getFiscalYear();
        if ($activeFY) $query->where('fiscal_year_id', $activeFY->id);

        if ($request->filled('type'))   $query->where('type', $request->type);
        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('return_number', 'like', "%$s%")
                ->orWhere('reason', 'like', "%$s%"));
        }

        $returns = $query->paginate(20)->withQueryString();

        $stats = [
            'total'     => $this->fyQuery(ReturnInvoice::class, $tenantId, $companyId)->count(),
            'sales'     => $this->fyQuery(ReturnInvoice::class, $tenantId, $companyId)->where('type', 'sales')->count(),
            'purchase'  => $this->fyQuery(ReturnInvoice::class, $tenantId, $companyId)->where('type', 'purchase')->count(),
            'confirmed' => $this->fyQuery(ReturnInvoice::class, $tenantId, $companyId)->where('status', 'confirmed')->count(),
        ];

        return view('warehouse.return-invoices.index', compact('returns', 'stats'));
    }

    // ─── فرم ایجاد ───────────────────────────────────────────────────────────
    public function create(Request $request)
    {
        Gate::authorize('access', 'return-invoices.create');
        [$tenantId, $companyId] = $this->ctx();

        $products      = Product::where('tenant_id', $tenantId)->where('is_active', true)->orderBy('title')->get();
        $units          = MeasurementUnit::where('tenant_id', $tenantId)->get();
        $warehouses     = Warehouse::where('tenant_id', $tenantId)->where('is_active', true)->orderBy('title')->get();
        $contacts       = Contact::where('tenant_id', $tenantId)->orderBy('name')->get();
        $fiscalYears    = FiscalYear::where('tenant_id', $tenantId)->where('company_id', $companyId)->where('is_closed', false)->get();
        $salesInvoices  = SalesInvoice::forTenant($tenantId, $companyId)->whereIn('status', ['confirmed','partially_paid','paid'])->latest()->get();
        $purchaseInvoices = PurchaseInvoice::forTenant($tenantId, $companyId)->whereIn('status', ['registered','paid'])->latest()->get();

        // پیش‌بارگذاری از فاکتور اصلی
        $sourceInvoice = null;
        $sourceType    = $request->input('type', 'sales');
        if ($sourceType === 'sales' && $request->filled('sales_invoice_id')) {
            $sourceInvoice = SalesInvoice::with('items.product', 'items.measurementUnit')
                ->forTenant($tenantId, $companyId)->findOrFail($request->sales_invoice_id);
        } elseif ($sourceType === 'purchase' && $request->filled('purchase_invoice_id')) {
            $sourceInvoice = PurchaseInvoice::with('items.product', 'items.measurementUnit')
                ->forTenant($tenantId, $companyId)->findOrFail($request->purchase_invoice_id);
        }

        return view('warehouse.return-invoices.create', compact(
            'products', 'units', 'warehouses', 'contacts', 'fiscalYears',
            'salesInvoices', 'purchaseInvoices', 'sourceInvoice', 'sourceType'
        ));
    }

    // ─── ذخیره ───────────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        Gate::authorize('access', 'return-invoices.create');
        [$tenantId, $companyId] = $this->ctx();

        $data = $request->validate([
            'type'                 => 'required|in:sales,purchase',
            'return_date'          => 'required|date',
            'warehouse_id'         => 'required|exists:warehouses,id',
            'contact_id'           => 'nullable|exists:contacts,id',
            'fiscal_year_id'       => 'nullable|exists:fiscal_years,id',
            'sales_invoice_id'     => 'nullable|exists:sales_invoices,id',
            'purchase_invoice_id'  => 'nullable|exists:purchase_invoices,id',
            'reason'               => 'nullable|string|max:255',
            'discount_amount'      => 'nullable|numeric|min:0',
            'tax_percent'          => 'nullable|numeric|min:0|max:100',
            'notes'                => 'nullable|string',
            'items'                => 'required|array|min:1',
            'items.*.product_id'       => 'required|exists:products,id',
            'items.*.quantity'         => 'required|numeric|min:0.0001',
            'items.*.unit_price'       => 'required|numeric|min:0',
            'items.*.discount_percent' => 'nullable|numeric|min:0|max:100',
            'items.*.measurement_unit_id' => 'nullable|exists:measurement_units,id',
        ]);

        $data['tenant_id']  = $tenantId;
        $data['company_id'] = $companyId;
        if (empty($data['fiscal_year_id'])) {
            $data['fiscal_year_id'] = $this->manager->getFiscalYear()?->id;
        }

        $returnInvoice = $this->service->create($data, $data['items']);

        return redirect()->route('warehouse.return-invoices.show', $returnInvoice)
            ->with('toast', ['type' => 'success', 'title' => 'برگشت ثبت شد', 'message' => 'سند برگشت با موفقیت ایجاد شد.']);
    }

    // ─── نمایش ───────────────────────────────────────────────────────────────
    public function show(ReturnInvoice $returnInvoice)
    {
        Gate::authorize('access', 'return-invoices.view');
        $returnInvoice->load(['items.product', 'items.measurementUnit', 'contact', 'warehouse', 'creator', 'confirmer', 'fiscalYear']);
        return view('warehouse.return-invoices.show', compact('returnInvoice'));
    }

    // ─── تأیید ───────────────────────────────────────────────────────────────
    public function confirm(ReturnInvoice $returnInvoice)
    {
        Gate::authorize('access', 'return-invoices.confirm');
        $this->service->confirm($returnInvoice);
        return redirect()->back()->with('toast', ['type' => 'success', 'title' => 'تأیید شد', 'message' => 'سند برگشت تأیید و موجودی اعمال شد.']);
    }

    // ─── لغو ─────────────────────────────────────────────────────────────────
    public function cancel(ReturnInvoice $returnInvoice)
    {
        Gate::authorize('access', 'return-invoices.cancel');
        $this->service->cancel($returnInvoice);
        return redirect()->back()->with('toast', ['type' => 'warning', 'title' => 'لغو شد', 'message' => 'سند برگشت لغو شد.']);
    }

    private function ctx(): array
    {
        return [$this->manager->getTenantId(), $this->manager->getCompanyId()];
    }
}
