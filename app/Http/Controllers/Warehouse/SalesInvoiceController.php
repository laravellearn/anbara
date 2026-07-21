<?php

namespace App\Http\Controllers\Warehouse;

use App\Models\Contact;
use App\Models\CostCenter;
use App\Models\FiscalYear;
use App\Models\MeasurementUnit;
use App\Models\Product;
use App\Models\SalesInvoice;
use App\Models\Warehouse;
use App\Http\Requests\Warehouse\StoreSalesInvoiceRequest;
use App\Services\SalesInvoiceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class SalesInvoiceController extends BaseController
{
    public function __construct(
        \App\Services\TenantManager $manager,
        private SalesInvoiceService $service
    ) {
        parent::__construct($manager);
    }

    // ─── index ────────────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        Gate::authorize('access', 'sales-invoices.view');
        [$tenantId, $companyId] = $this->ctx();

        $query = SalesInvoice::with(['customer', 'creator'])
            ->forTenant($tenantId, $companyId)
            ->latest('invoice_date');

        // ─── فیلتر سال مالی ───────────────────────────────────────────────────
        $activeFiscalYear = $this->manager->getFiscalYear();
        if ($activeFiscalYear) {
            $query->where('fiscal_year_id', $activeFiscalYear->id);
        }

        if ($request->filled('status'))      { $query->where('status', $request->status); }
        if ($request->filled('customer_id')) { $query->where('customer_id', $request->customer_id); }
        if ($request->filled('date_from'))   { $query->whereDate('invoice_date', '>=', $request->date_from); }
        if ($request->filled('date_to'))     { $query->whereDate('invoice_date', '<=', $request->date_to); }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q
                ->where('invoice_number', 'like', "%{$s}%")
                ->orWhere('reference_number', 'like', "%{$s}%")
            );
        }

        $invoices   = $query->paginate($request->per_page ?? 20);
        $customers  = Contact::where('tenant_id', $tenantId)->orderBy('name')->get();

        $stats = [
            'total'         => $this->fyQuery(SalesInvoice::class, $tenantId, $companyId)->count(),
            'draft'         => $this->fyQuery(SalesInvoice::class, $tenantId, $companyId)->where('status', 'draft')->count(),
            'unpaid'        => $this->fyQuery(SalesInvoice::class, $tenantId, $companyId)->whereIn('status', ['confirmed', 'partially_paid'])->count(),
            'paid'          => $this->fyQuery(SalesInvoice::class, $tenantId, $companyId)->where('status', 'paid')->count(),
            'total_revenue' => $this->fyQuery(SalesInvoice::class, $tenantId, $companyId)->whereIn('status', ['confirmed','partially_paid','paid'])->sum('paid_amount'),
        ];

        return view('warehouse.sales-invoices.index', compact('invoices', 'customers', 'stats'));
    }

    // ─── create ───────────────────────────────────────────────────────────────
    public function create()
    {
        Gate::authorize('access', 'sales-invoices.create');
        [$tenantId, $companyId] = $this->ctx();

        $customers  = Contact::where('tenant_id', $tenantId)->orderBy('name')->get();
        $products   = Product::where('tenant_id', $tenantId)->where('is_active', true)->orderBy('title')->get();
        $units      = MeasurementUnit::where('tenant_id', $tenantId)->get();
        $warehouses = Warehouse::where('tenant_id', $tenantId)->where('is_active', true)->get();
        $fiscalYears = FiscalYear::where('tenant_id', $tenantId)->where('is_active', true)->get();
        $costCenters = CostCenter::where('tenant_id', $tenantId)->get();

        return view('warehouse.sales-invoices.create', compact(
            'customers', 'products', 'units', 'warehouses', 'fiscalYears', 'costCenters'
        ));
    }

    // ─── store ────────────────────────────────────────────────────────────────
    public function store(StoreSalesInvoiceRequest $request)
    {
        Gate::authorize('access', 'sales-invoices.create');
        [$tenantId, $companyId] = $this->ctx();

        try {
            DB::transaction(function () use ($request, $tenantId, $companyId) {
                $invoice = SalesInvoice::create(array_merge($request->except('items'), [
                    'tenant_id'      => $tenantId,
                    'company_id'     => $companyId,
                    'created_by'     => auth()->id(),
                    'status'         => SalesInvoice::STATUS_DRAFT,
                    'invoice_number' => $this->service->generateInvoiceNumber($tenantId),
                ]));

                foreach ($request->items as $i => $item) {
                    $qty   = (float)$item['quantity'];
                    $price = (float)$item['unit_price'];
                    $disc  = (float)($item['discount_amount'] ?? 0);
                    $invoice->items()->create(array_merge($item, [
                        'sort_order'  => $i,
                        'total_price' => round($qty * $price - $disc, 4),
                    ]));
                }

                $this->service->recalculate($invoice);
            });

            return redirect()->route('warehouse.sales-invoices.index')->with('toast', [
                'message' => 'فاکتور فروش با موفقیت ثبت شد.', 'type' => 'success', 'title' => 'ثبت فاکتور',
            ]);
        } catch (\Exception $e) {
            \Log::error($e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            return redirect()->back()->withErrors(['error' => 'خطا در ثبت فاکتور'])->withInput();
        }
    }

    // ─── show ─────────────────────────────────────────────────────────────────
    public function show(SalesInvoice $salesInvoice)
    {
        Gate::authorize('access', 'sales-invoices.view');
        $this->authorizeRecord($salesInvoice);

        $salesInvoice->load(['items.product', 'items.measurementUnit', 'customer', 'warehouse', 'creator', 'confirmer']);
        $statusLabels = SalesInvoice::statusLabels();
        $statusColors = SalesInvoice::statusColors();

        return view('warehouse.sales-invoices.show', compact('salesInvoice', 'statusLabels', 'statusColors'));
    }

    // ─── edit ─────────────────────────────────────────────────────────────────
    public function edit(SalesInvoice $salesInvoice)
    {
        Gate::authorize('access', 'sales-invoices.edit');
        $this->authorizeRecord($salesInvoice);

        if (!$salesInvoice->isEditable()) {
            return redirect()->route('warehouse.sales-invoices.show', $salesInvoice)->with('toast', [
                'message' => 'فقط فاکتورهای پیش‌نویس قابل ویرایش هستند.', 'type' => 'warning', 'title' => 'ویرایش مجاز نیست',
            ]);
        }

        $salesInvoice->load('items');
        [$tenantId] = $this->ctx();
        $customers   = Contact::where('tenant_id', $tenantId)->orderBy('name')->get();
        $products    = Product::where('tenant_id', $tenantId)->where('is_active', true)->orderBy('title')->get();
        $units       = MeasurementUnit::where('tenant_id', $tenantId)->get();
        $warehouses  = Warehouse::where('tenant_id', $tenantId)->where('is_active', true)->get();
        $fiscalYears = FiscalYear::where('tenant_id', $tenantId)->where('is_active', true)->get();
        $costCenters = CostCenter::where('tenant_id', $tenantId)->get();

        return view('warehouse.sales-invoices.edit', compact(
            'salesInvoice', 'customers', 'products', 'units', 'warehouses', 'fiscalYears', 'costCenters'
        ));
    }

    // ─── update ───────────────────────────────────────────────────────────────
    public function update(StoreSalesInvoiceRequest $request, SalesInvoice $salesInvoice)
    {
        Gate::authorize('access', 'sales-invoices.edit');
        $this->authorizeRecord($salesInvoice);

        if (!$salesInvoice->isEditable()) {
            abort(403, 'فاکتور قابل ویرایش نیست.');
        }

        try {
            DB::transaction(function () use ($request, $salesInvoice) {
                $salesInvoice->update($request->except('items'));
                $salesInvoice->items()->delete();

                foreach ($request->items as $i => $item) {
                    $qty   = (float)$item['quantity'];
                    $price = (float)$item['unit_price'];
                    $disc  = (float)($item['discount_amount'] ?? 0);
                    $salesInvoice->items()->create(array_merge($item, [
                        'sort_order'  => $i,
                        'total_price' => round($qty * $price - $disc, 4),
                    ]));
                }

                $this->service->recalculate($salesInvoice);
            });

            return redirect()->route('warehouse.sales-invoices.show', $salesInvoice)->with('toast', [
                'message' => 'فاکتور با موفقیت ویرایش شد.', 'type' => 'success', 'title' => 'ویرایش فاکتور',
            ]);
        } catch (\Exception $e) {
            \Log::error($e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            return redirect()->back()->withErrors(['error' => 'خطا در ویرایش فاکتور'])->withInput();
        }
    }

    // ─── destroy ──────────────────────────────────────────────────────────────
    public function destroy(SalesInvoice $salesInvoice)
    {
        Gate::authorize('access', 'sales-invoices.delete');
        $this->authorizeRecord($salesInvoice);

        if (!$salesInvoice->isEditable()) {
            return redirect()->back()->with('toast', [
                'message' => 'فقط فاکتورهای پیش‌نویس قابل حذف هستند.', 'type' => 'warning', 'title' => 'حذف مجاز نیست',
            ]);
        }

        $salesInvoice->delete();
        return redirect()->route('warehouse.sales-invoices.index')->with('toast', [
            'message' => 'فاکتور حذف شد.', 'type' => 'success', 'title' => 'حذف فاکتور',
        ]);
    }

    // ─── confirm ──────────────────────────────────────────────────────────────
    public function confirm(Request $request, SalesInvoice $salesInvoice)
    {
        Gate::authorize('access', 'sales-invoices.confirm');
        $this->authorizeRecord($salesInvoice);

        try {
            $this->service->confirm($salesInvoice, auth()->id(), (bool)$request->issue_document);
            return redirect()->route('warehouse.sales-invoices.show', $salesInvoice)->with('toast', [
                'message' => 'فاکتور تأیید شد.', 'type' => 'success', 'title' => 'تأیید فاکتور',
            ]);
        } catch (\Exception $e) {
            \Log::error($e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            return redirect()->back()->withErrors(['error' => 'خطا در تأیید فاکتور']);
        }
    }

    // ─── register payment ─────────────────────────────────────────────────────
    public function registerPayment(Request $request, SalesInvoice $salesInvoice)
    {
        Gate::authorize('access', 'sales-invoices.pay');
        $this->authorizeRecord($salesInvoice);

        $request->validate(['amount' => 'required|numeric|min:0.01']);

        try {
            $this->service->registerPayment($salesInvoice, (float)$request->amount);
            return redirect()->route('warehouse.sales-invoices.show', $salesInvoice)->with('toast', [
                'message' => 'پرداخت با موفقیت ثبت شد.', 'type' => 'success', 'title' => 'ثبت پرداخت',
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    // ─── cancel ───────────────────────────────────────────────────────────────
    public function cancel(SalesInvoice $salesInvoice)
    {
        Gate::authorize('access', 'sales-invoices.delete');
        $this->authorizeRecord($salesInvoice);

        try {
            $this->service->cancel($salesInvoice);
            return redirect()->route('warehouse.sales-invoices.show', $salesInvoice)->with('toast', [
                'message' => 'فاکتور لغو شد.', 'type' => 'success', 'title' => 'لغو فاکتور',
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    // ─── print ────────────────────────────────────────────────────────────────
    public function print(SalesInvoice $salesInvoice)
    {
        Gate::authorize('access', 'sales-invoices.view');
        $this->authorizeRecord($salesInvoice);
        $salesInvoice->load(['items.product', 'items.measurementUnit', 'customer', 'warehouse', 'creator']);
        return view('warehouse.sales-invoices.print', compact('salesInvoice'));
    }

    // ─── private helpers ──────────────────────────────────────────────────────
    private function authorizeRecord(SalesInvoice $invoice): void
    {
        [$tenantId, $companyId] = $this->ctx();
        if ($invoice->tenant_id !== $tenantId || $invoice->company_id !== $companyId) {
            abort(403);
        }
    }

    private function ctx(): array
    {
        return [$this->manager->getTenantId(), $this->manager->getCompanyId()];
    }
}
