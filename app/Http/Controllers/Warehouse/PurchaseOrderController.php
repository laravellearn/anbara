<?php

namespace App\Http\Controllers\Warehouse;

use App\Models\CostCenter;
use App\Models\FiscalYear;
use App\Models\MeasurementUnit;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\Warehouse;
use App\Http\Requests\Warehouse\StorePurchaseOrderRequest;
use App\Http\Requests\Warehouse\UpdatePurchaseOrderRequest;
use App\Services\PurchaseOrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class PurchaseOrderController extends BaseController
{
    public function __construct(
        \App\Services\TenantManager $manager,
        private PurchaseOrderService $poService
    ) {
        parent::__construct($manager);
    }

    // ─── index ────────────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        Gate::authorize('access', 'purchase-orders.view');

        [$tenantId, $companyId] = $this->ctx();

        $query = PurchaseOrder::with(['supplier', 'warehouse', 'creator'])
            ->forTenant($tenantId, $companyId)
            ->withCount('items')
            ->latest('order_date');

        // ─── فیلتر سال مالی ───────────────────────────────────────────────────
        $activeFiscalYear = $this->manager->getFiscalYear();
        if ($activeFiscalYear) {
            $query->where('fiscal_year_id', $activeFiscalYear->id);
        }

        if ($request->filled('status'))      { $query->withStatus($request->status); }
        if ($request->filled('supplier_id')) { $query->where('supplier_id', $request->supplier_id); }
        if ($request->filled('date_from'))   { $query->whereDate('order_date', '>=', $request->date_from); }
        if ($request->filled('date_to'))     { $query->whereDate('order_date', '<=', $request->date_to); }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q
                ->where('po_number', 'like', "%{$s}%")
                ->orWhere('reference_number', 'like', "%{$s}%")
            );
        }

        $orders    = $query->paginate($request->per_page ?? 20);
        $suppliers = \App\Models\Contact::where('tenant_id', $tenantId)->orderBy('name')->get();

        $stats = [
            'total'   => $this->fyQuery(PurchaseOrder::class, $tenantId, $companyId)->count(),
            'draft'   => $this->fyQuery(PurchaseOrder::class, $tenantId, $companyId)->withStatus('draft')->count(),
            'pending' => $this->fyQuery(PurchaseOrder::class, $tenantId, $companyId)->whereIn('status', ['confirmed','sent'])->count(),
            'open'    => $this->fyQuery(PurchaseOrder::class, $tenantId, $companyId)->whereIn('status', ['confirmed','sent','partial_received'])->count(),
        ];

        if ($request->ajax() || $request->input('ajax')) {
            return response()->json([
                'html'  => view('warehouse.purchase-orders._table', compact('orders'))->render(),
                'total' => $orders->total(),
            ]);
        }

        return view('warehouse.purchase-orders.index', compact('orders', 'suppliers', 'stats'));
    }

    // ─── create ───────────────────────────────────────────────────────────────
    public function create()
    {
        Gate::authorize('access', 'purchase-orders.create');
        [$warehouses, $products, $units, $costCenters, $fiscalYears, $suppliers] = $this->formData();

        return view('warehouse.purchase-orders.create', compact(
            'warehouses', 'products', 'units', 'costCenters', 'fiscalYears', 'suppliers'
        ));
    }

    // ─── store ────────────────────────────────────────────────────────────────
    public function store(StorePurchaseOrderRequest $request)
    {
        Gate::authorize('access', 'purchase-orders.create');
        [$tenantId, $companyId] = $this->ctx();

        try {
            DB::transaction(function () use ($request, $tenantId, $companyId) {
                $po = PurchaseOrder::create(array_merge($request->except('items'), [
                    'tenant_id'  => $tenantId,
                    'company_id' => $companyId,
                    'created_by' => auth()->id(),
                    'status'     => PurchaseOrder::STATUS_DRAFT,
                    'po_number'  => $this->poService->generatePoNumber($tenantId),
                ]));
                foreach ($request->items as $i => $item) {
                    $po->items()->create(array_merge($item, ['sort_order' => $i]));
                }
            });

            return redirect()->route('warehouse.purchase-orders.index')->with('toast', [
                'message' => 'سفارش خرید با موفقیت ثبت شد.', 'type' => 'success', 'title' => 'ثبت PO',
            ]);
        } catch (\Exception $e) {
            \Log::error($e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            return redirect()->back()->withErrors(['error' => 'خطایی رخ داد. لطفاً مجدداً تلاش کنید.'])->withInput();
        }
    }

    // ─── show ─────────────────────────────────────────────────────────────────
    public function show(PurchaseOrder $purchaseOrder)
    {
        Gate::authorize('access', 'purchase-orders.view');
        $this->authorizePo($purchaseOrder);

        $purchaseOrder->load([
            'items.product', 'items.measurementUnit', 'items.warehouseDocument',
            'supplier', 'warehouse', 'fiscalYear', 'costCenter', 'creator', 'confirmer',
        ]);

        return view('warehouse.purchase-orders.show', ['po' => $purchaseOrder]);
    }

    // ─── print ────────────────────────────────────────────────────────────────
    public function print(PurchaseOrder $purchaseOrder)
    {
        Gate::authorize('access', 'purchase-orders.view');
        $this->authorizePo($purchaseOrder);

        $purchaseOrder->load([
            'items.product', 'items.measurementUnit',
            'supplier', 'warehouse', 'costCenter', 'fiscalYear', 'creator', 'confirmer',
        ]);

        return view('warehouse.purchase-orders.print', ['po' => $purchaseOrder]);
    }

    // ─── edit ─────────────────────────────────────────────────────────────────
    public function edit(PurchaseOrder $purchaseOrder)
    {
        Gate::authorize('access', 'purchase-orders.edit');
        $this->authorizePo($purchaseOrder);

        if (!$purchaseOrder->isEditable()) {
            return redirect()->route('warehouse.purchase-orders.show', $purchaseOrder)->with('toast', [
                'message' => 'فقط سفارش‌های پیش‌نویس قابل ویرایش هستند.', 'type' => 'warning', 'title' => '',
            ]);
        }

        $purchaseOrder->load('items');
        [$warehouses, $products, $units, $costCenters, $fiscalYears, $suppliers] = $this->formData();

        return view('warehouse.purchase-orders.edit', compact(
            'purchaseOrder', 'warehouses', 'products', 'units', 'costCenters', 'fiscalYears', 'suppliers'
        ));
    }

    // ─── update ───────────────────────────────────────────────────────────────
    public function update(UpdatePurchaseOrderRequest $request, PurchaseOrder $purchaseOrder)
    {
        Gate::authorize('access', 'purchase-orders.edit');
        $this->authorizePo($purchaseOrder);

        if (!$purchaseOrder->isEditable()) {
            return redirect()->route('warehouse.purchase-orders.show', $purchaseOrder)->with('toast', [
                'message' => 'فقط پیش‌نویس قابل ویرایش است.', 'type' => 'warning', 'title' => '',
            ]);
        }

        try {
            DB::transaction(function () use ($request, $purchaseOrder) {
                $purchaseOrder->update($request->except('items'));
                $purchaseOrder->items()->delete();
                foreach ($request->items as $i => $item) {
                    $purchaseOrder->items()->create(array_merge($item, ['sort_order' => $i]));
                }
            });

            return redirect()->route('warehouse.purchase-orders.show', $purchaseOrder)->with('toast', [
                'message' => 'سفارش ویرایش شد.', 'type' => 'success', 'title' => '',
            ]);
        } catch (\Exception $e) {
            \Log::error($e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            return redirect()->back()->withErrors(['error' => 'خطایی رخ داد. لطفاً مجدداً تلاش کنید.'])->withInput();
        }
    }

    // ─── destroy ──────────────────────────────────────────────────────────────
    public function destroy(PurchaseOrder $purchaseOrder)
    {
        Gate::authorize('access', 'purchase-orders.delete');
        $this->authorizePo($purchaseOrder);

        if (!$purchaseOrder->isEditable()) {
            return redirect()->back()->with('toast', ['message' => 'فقط پیش‌نویس قابل حذف است.', 'type' => 'warning', 'title' => '']);
        }

        $purchaseOrder->delete();
        return redirect()->route('warehouse.purchase-orders.index')->with('toast', [
            'message' => 'سفارش حذف شد.', 'type' => 'success', 'title' => '',
        ]);
    }

    // ─── workflow ─────────────────────────────────────────────────────────────
    public function confirm(PurchaseOrder $purchaseOrder)
    {
        Gate::authorize('access', 'purchase-orders.confirm');
        $this->authorizePo($purchaseOrder);
        try {
            $this->poService->confirm($purchaseOrder, auth()->id());
            return redirect()->route('warehouse.purchase-orders.show', $purchaseOrder)->with('toast', [
                'message' => 'سفارش تأیید شد.', 'type' => 'success', 'title' => 'تأیید PO',
            ]);
        } catch (\Exception $e) {
            \Log::error($e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            return redirect()->back()->withErrors(['error' => 'خطایی رخ داد. لطفاً مجدداً تلاش کنید.']);
        }
    }

    public function markSent(PurchaseOrder $purchaseOrder)
    {
        Gate::authorize('access', 'purchase-orders.confirm');
        $this->authorizePo($purchaseOrder);
        try {
            $this->poService->markSent($purchaseOrder);
            return redirect()->route('warehouse.purchase-orders.show', $purchaseOrder)->with('toast', [
                'message' => 'سفارش به تأمین‌کننده ارسال شد.', 'type' => 'info', 'title' => 'ارسال PO',
            ]);
        } catch (\Exception $e) {
            \Log::error($e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            return redirect()->back()->withErrors(['error' => 'خطایی رخ داد. لطفاً مجدداً تلاش کنید.']);
        }
    }

    public function receiveForm(PurchaseOrder $purchaseOrder)
    {
        Gate::authorize('access', 'purchase-orders.receive');
        $this->authorizePo($purchaseOrder);

        if (!$purchaseOrder->canReceive()) {
            return redirect()->route('warehouse.purchase-orders.show', $purchaseOrder)->with('toast', [
                'message' => 'امکان ثبت دریافت برای این سفارش وجود ندارد.', 'type' => 'warning', 'title' => '',
            ]);
        }

        $purchaseOrder->load('items.product', 'items.measurementUnit');
        return view('warehouse.purchase-orders.receive', ['po' => $purchaseOrder]);
    }

    public function receive(Request $request, PurchaseOrder $purchaseOrder)
    {
        Gate::authorize('access', 'purchase-orders.receive');
        $this->authorizePo($purchaseOrder);

        $request->validate([
            'items'            => ['required', 'array'],
            'items.*.item_id'  => ['required', 'integer'],
            'items.*.quantity' => ['required', 'numeric', 'min:0'],
        ]);

        try {
            $received = collect($request->items)->filter(fn($i) => (float)$i['quantity'] > 0)->values()->toArray();
            if (empty($received)) {
                return redirect()->back()->withErrors(['error' => 'حداقل یک ردیف با مقدار بیشتر از صفر وارد کنید.']);
            }
            $doc = $this->poService->receive($purchaseOrder, $received, auth()->id());

            return redirect()->route('warehouse.purchase-orders.show', $purchaseOrder)->with('toast', [
                'message' => "دریافت ثبت شد. سند انبار {$doc->document_number} ایجاد و تأیید گردید.",
                'type'    => 'success', 'title' => 'ثبت دریافت',
            ]);
        } catch (\Exception $e) {
            \Log::error($e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            return redirect()->back()->withErrors(['error' => 'خطایی رخ داد. لطفاً مجدداً تلاش کنید.']);
        }
    }

    public function close(PurchaseOrder $purchaseOrder)
    {
        Gate::authorize('access', 'purchase-orders.confirm');
        $this->authorizePo($purchaseOrder);
        try {
            $this->poService->close($purchaseOrder, auth()->id());
            return redirect()->route('warehouse.purchase-orders.show', $purchaseOrder)->with('toast', [
                'message' => 'سفارش بسته شد.', 'type' => 'dark', 'title' => 'بستن PO',
            ]);
        } catch (\Exception $e) {
            \Log::error($e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            return redirect()->back()->withErrors(['error' => 'خطایی رخ داد. لطفاً مجدداً تلاش کنید.']);
        }
    }

    public function cancel(Request $request, PurchaseOrder $purchaseOrder)
    {
        Gate::authorize('access', 'purchase-orders.confirm');
        $this->authorizePo($purchaseOrder);
        try {
            $this->poService->cancel($purchaseOrder, $request->cancellation_reason);
            return redirect()->route('warehouse.purchase-orders.show', $purchaseOrder)->with('toast', [
                'message' => 'سفارش لغو شد.', 'type' => 'danger', 'title' => 'لغو PO',
            ]);
        } catch (\Exception $e) {
            \Log::error($e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            return redirect()->back()->withErrors(['error' => 'خطایی رخ داد. لطفاً مجدداً تلاش کنید.']);
        }
    }

    // ─── helpers ──────────────────────────────────────────────────────────────
    private function authorizePo(PurchaseOrder $po): void
    {
        if ($po->tenant_id !== $this->manager->getTenantId() || $po->company_id !== $this->manager->getCompanyId()) {
            abort(403);
        }
    }

    private function ctx(): array
    {
        return [$this->manager->getTenantId(), $this->manager->getCompanyId()];
    }

    private function formData(): array
    {
        $tenantId = $this->manager->getTenantId();
        return [
            Warehouse::where('tenant_id', $tenantId)->where('is_active', true)->orderBy('title')->get(),
            Product::where('tenant_id', $tenantId)->where('is_active', true)->orderBy('title')->get(),
            MeasurementUnit::where('tenant_id', $tenantId)->where('is_active', true)->orderBy('title')->get(),
            CostCenter::where('tenant_id', $tenantId)->where('is_active', true)->orderBy('title')->get(),
            FiscalYear::where('tenant_id', $tenantId)->orderBy('start_date', 'desc')->get(),
            \App\Models\Contact::where('tenant_id', $tenantId)->orderBy('name')->get(),
        ];
    }
}
