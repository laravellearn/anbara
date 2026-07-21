<?php

namespace App\Http\Controllers\Warehouse;

use App\Models\Contact;
use App\Models\CostCenter;
use App\Models\FiscalYear;
use App\Models\MeasurementUnit;
use App\Models\Product;
use App\Models\Quotation;
use App\Models\Warehouse;
use App\Services\QuotationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class QuotationController extends BaseController
{
    public function __construct(
        \App\Services\TenantManager $manager,
        private QuotationService $service
    ) {
        parent::__construct($manager);
    }

    public function index(Request $request)
    {
        Gate::authorize('access', 'quotations.view');
        [$tenantId, $companyId] = $this->ctx();

        $query = Quotation::with('customer')
            ->forTenant($tenantId, $companyId)
            ->latest('quotation_date');

        // ─── فیلتر سال مالی ───────────────────────────────────────────────────
        $activeFiscalYear = $this->manager->getFiscalYear();
        if ($activeFiscalYear) {
            $query->where('fiscal_year_id', $activeFiscalYear->id);
        }

        if ($request->filled('status'))      $query->where('status', $request->status);
        if ($request->filled('customer_id')) $query->where('customer_id', $request->customer_id);
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('quotation_number', 'like', "%$s%")
                ->orWhere('description', 'like', "%$s%"));
        }

        $quotations = $query->paginate(20)->withQueryString();
        $customers  = Contact::where('tenant_id', $tenantId)->where('type', 'customer')->orderBy('name')->get();

        $stats = [
            'total'    => Quotation::forTenant($tenantId, $companyId)->count(),
            'draft'    => Quotation::forTenant($tenantId, $companyId)->where('status', 'draft')->count(),
            'sent'     => Quotation::forTenant($tenantId, $companyId)->where('status', 'sent')->count(),
            'accepted' => Quotation::forTenant($tenantId, $companyId)->where('status', 'accepted')->count(),
        ];

        return view('warehouse.quotations.index', compact('quotations', 'customers', 'stats'));
    }

    public function create()
    {
        Gate::authorize('access', 'quotations.create');
        [$tenantId, $companyId] = $this->ctx();
        return view('warehouse.quotations.create', array_merge($this->formData($tenantId, $companyId), [
            'quotation' => new Quotation(),
        ]));
    }

    public function store(Request $request)
    {
        Gate::authorize('access', 'quotations.create');
        [$tenantId, $companyId] = $this->ctx();
        $data = $this->validateQuotation($request);
        $quotation = $this->service->createOrUpdate($data, $tenantId, $companyId, auth()->id());
        return redirect()->route('warehouse.quotations.show', $quotation)
            ->with('success', 'پیش‌فاکتور ' . $quotation->quotation_number . ' ثبت شد.');
    }

    public function show(Quotation $quotation)
    {
        Gate::authorize('access', 'quotations.view');
        $this->authorizeTenant($quotation);
        $quotation->load('items.product', 'items.measurementUnit', 'customer', 'warehouse', 'creator');
        return view('warehouse.quotations.show', compact('quotation'));
    }

    public function edit(Quotation $quotation)
    {
        Gate::authorize('access', 'quotations.edit');
        $this->authorizeTenant($quotation);
        abort_unless($quotation->isEditable(), 403, 'پیش‌فاکتور قابل ویرایش نیست.');
        [$tenantId, $companyId] = $this->ctx();
        $quotation->load('items');
        return view('warehouse.quotations.edit', array_merge($this->formData($tenantId, $companyId), compact('quotation')));
    }

    public function update(Request $request, Quotation $quotation)
    {
        Gate::authorize('access', 'quotations.edit');
        $this->authorizeTenant($quotation);
        abort_unless($quotation->isEditable(), 403);
        [$tenantId, $companyId] = $this->ctx();
        $data = $this->validateQuotation($request);
        $this->service->createOrUpdate($data, $tenantId, $companyId, auth()->id(), $quotation);
        return redirect()->route('warehouse.quotations.show', $quotation)
            ->with('success', 'پیش‌فاکتور بروزرسانی شد.');
    }

    public function destroy(Quotation $quotation)
    {
        Gate::authorize('access', 'quotations.delete');
        $this->authorizeTenant($quotation);
        abort_unless($quotation->isEditable(), 403);
        $quotation->delete();
        return redirect()->route('warehouse.quotations.index')->with('success', 'پیش‌فاکتور حذف شد.');
    }

    public function updateStatus(Request $request, Quotation $quotation)
    {
        Gate::authorize('access', 'quotations.edit');
        $this->authorizeTenant($quotation);
        $request->validate(['status' => 'required|in:draft,sent,accepted,rejected,expired']);
        $quotation->update(['status' => $request->status]);
        return back()->with('success', 'وضعیت پیش‌فاکتور تغییر کرد.');
    }

    public function convertToInvoice(Quotation $quotation)
    {
        Gate::authorize('access', 'quotations.convert');
        $this->authorizeTenant($quotation);
        abort_unless($quotation->canConvert(), 403, 'فقط پیش‌فاکتورهای پذیرفته شده قابل تبدیل هستند.');
        $invoice = $this->service->convertToInvoice($quotation);
        return redirect()->route('warehouse.sales-invoices.show', $invoice)
            ->with('success', 'پیش‌فاکتور به فاکتور فروش ' . $invoice->invoice_number . ' تبدیل شد.');
    }

    public function print(Quotation $quotation)
    {
        Gate::authorize('access', 'quotations.view');
        $this->authorizeTenant($quotation);
        $quotation->load('items.product', 'items.measurementUnit', 'customer', 'warehouse', 'creator');
        return view('warehouse.quotations.print', compact('quotation'));
    }

    // ─── Private helpers ──────────────────────────────────────────────────────
    private function formData(int $tenantId, int $companyId): array
    {
        return [
            'customers'   => Contact::where('tenant_id', $tenantId)->where('type', 'customer')->orderBy('name')->get(),
            'warehouses'  => Warehouse::where('tenant_id', $tenantId)->where('is_active', true)->orderBy('title')->get(),
            'fiscalYears' => FiscalYear::where('tenant_id', $tenantId)->orderByDesc('start_date')->get(),
            'costCenters' => CostCenter::where('tenant_id', $tenantId)->get(),
            'products'    => Product::where('tenant_id', $tenantId)->where('is_active', true)->orderBy('title')->get(),
            'units'       => MeasurementUnit::where('tenant_id', $tenantId)->orderBy('title')->get(),
        ];
    }

    private function validateQuotation(Request $request): array
    {
        return $request->validate([
            'customer_id'                    => 'nullable|exists:contacts,id',
            'warehouse_id'                   => 'nullable|exists:warehouses,id',
            'fiscal_year_id'                 => 'nullable|exists:fiscal_years,id',
            'cost_center_id'                 => 'nullable|exists:cost_centers,id',
            'quotation_date'                 => 'required|date',
            'valid_until'                    => 'nullable|date|after_or_equal:quotation_date',
            'reference_number'               => 'nullable|string|max:100',
            'description'                    => 'nullable|string|max:1000',
            'terms'                          => 'nullable|string|max:2000',
            'discount_percent'               => 'nullable|numeric|min:0|max:100',
            'tax_percent'                    => 'nullable|numeric|min:0|max:100',
            'items'                          => 'required|array|min:1',
            'items.*.product_id'             => 'required|exists:products,id',
            'items.*.measurement_unit_id'    => 'nullable|exists:measurement_units,id',
            'items.*.quantity'               => 'required|numeric|min:0.001',
            'items.*.unit_price'             => 'required|numeric|min:0',
            'items.*.discount_amount'        => 'nullable|numeric|min:0',
        ]);
    }
}
