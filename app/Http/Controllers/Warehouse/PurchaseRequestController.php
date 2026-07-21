<?php

namespace App\Http\Controllers\Warehouse;

use App\Models\CostCenter;
use App\Models\FiscalYear;
use App\Models\MeasurementUnit;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseRequest;
use App\Models\PurchaseRequestItem;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class PurchaseRequestController extends BaseController
{
    public function __construct(\App\Services\TenantManager $manager)
    {
        parent::__construct($manager);
    }

    public function index(Request $request)
    {
        Gate::authorize('access', 'purchase-requests.view');
        [$tenantId, $companyId] = $this->ctx();

        // ─── ذخیره / بازیابی فیلترها در session ───────────────────────────
        $sessionKey = 'filters.purchase_requests';
        if ($request->has('_clear_filters')) {
            session()->forget($sessionKey);
            return redirect()->route('warehouse.purchase-requests.index');
        }
        if ($request->hasAny(['status','priority','date_from','date_to','search','per_page'])) {
            session([$sessionKey => $request->only(['status','priority','date_from','date_to','search','per_page'])]);
        }
        $filters = session($sessionKey, []);
        $request->mergeIfMissing($filters);

        $query = PurchaseRequest::with(['requester', 'warehouse'])
            ->forTenant($tenantId, $companyId)
            ->withCount('items')
            ->latest('request_date');

        if ($request->filled('status'))   { $query->where('status', $request->status); }
        if ($request->filled('priority')) { $query->where('priority', $request->priority); }
        if ($request->filled('date_from')){ $query->whereDate('request_date', '>=', $request->date_from); }
        if ($request->filled('date_to'))  { $query->whereDate('request_date', '<=', $request->date_to); }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('pr_number', 'like', "%$s%")->orWhere('reason', 'like', "%$s%"));
        }

        $requests = $query->paginate($request->per_page ?? 20)->withQueryString();

        $stats = [
            'total'     => PurchaseRequest::forTenant($tenantId, $companyId)->count(),
            'draft'     => PurchaseRequest::forTenant($tenantId, $companyId)->where('status', 'draft')->count(),
            'submitted' => PurchaseRequest::forTenant($tenantId, $companyId)->where('status', 'submitted')->count(),
            'approved'  => PurchaseRequest::forTenant($tenantId, $companyId)->where('status', 'approved')->count(),
        ];

        return view('warehouse.purchase-requests.index', compact('requests', 'stats', 'filters'));
    }

    // ─── Bulk action ─────────────────────────────────────────────────────────
    public function bulkAction(Request $request)
    {
        Gate::authorize('access', 'purchase-requests.delete');
        [$tenantId, $companyId] = $this->ctx();

        $request->validate([
            'action' => 'required|in:delete',
            'ids'    => 'required|array|min:1',
            'ids.*'  => 'integer',
        ]);

        $scope = PurchaseRequest::forTenant($tenantId, $companyId)
            ->whereIn('id', $request->ids);

        if ($request->action === 'delete') {
            $deletable = (clone $scope)->where('status', 'draft')->get();
            foreach ($deletable as $pr) {
                $pr->items()->delete();
                $pr->delete();
            }
            $count = $deletable->count();
            return back()->with('success', "{$count} درخواست خرید حذف شد.");
        }

        return back()->with('error', 'عملیات نامعتبر است.');
    }

    public function create()
    {
        Gate::authorize('access', 'purchase-requests.create');
        [$warehouses, $products, $units, $costCenters, $fiscalYears] = $this->formData();
        $purchaseRequest = new PurchaseRequest();
        return view('warehouse.purchase-requests.create', compact('warehouses', 'products', 'units', 'costCenters', 'fiscalYears', 'purchaseRequest'));
    }

    public function store(Request $request)
    {
        Gate::authorize('access', 'purchase-requests.create');
        $data = $request->validate([
            'warehouse_id'      => 'nullable|exists:warehouses,id',
            'fiscal_year_id'    => 'nullable|exists:fiscal_years,id',
            'cost_center_id'    => 'nullable|exists:cost_centers,id',
            'request_date'      => 'required|date',
            'required_by_date'  => 'nullable|date|after_or_equal:request_date',
            'priority'          => 'required|in:low,normal,high,urgent',
            'reason'            => 'nullable|string|max:1000',
            'notes'             => 'nullable|string|max:1000',
            'items'             => 'required|array|min:1',
            'items.*.product_id'=> 'required|exists:products,id',
            'items.*.quantity_requested' => 'required|numeric|min:0.001',
        ]);

        DB::transaction(function () use ($data, $request) {
            $tenantId  = $this->manager->getTenantId();
            $companyId = $this->manager->getCompanyId();

            $pr = PurchaseRequest::create([
                ...$data,
                'tenant_id'  => $tenantId,
                'company_id' => $companyId,
                'pr_number'  => $this->generateNumber($tenantId),
                'requester_id' => auth()->id(),
                'created_by'   => auth()->id(),
            ]);

            foreach ($data['items'] as $idx => $item) {
                $pr->items()->create([
                    'product_id'          => $item['product_id'],
                    'measurement_unit_id' => $item['measurement_unit_id'] ?? null,
                    'quantity_requested'  => $item['quantity_requested'],
                    'estimated_unit_price'=> $item['estimated_unit_price'] ?? null,
                    'description'         => $item['description'] ?? null,
                    'sort_order'          => $idx,
                ]);
            }
        });

        return redirect()->route('warehouse.purchase-requests.index')
            ->with('success', 'درخواست خرید با موفقیت ثبت شد.');
    }

    public function show(PurchaseRequest $purchaseRequest)
    {
        Gate::authorize('access', 'purchase-requests.view');
        $this->authorizeTenant($purchaseRequest);
        $purchaseRequest->load(['items.product', 'items.measurementUnit', 'requester', 'approver', 'warehouse', 'costCenter', 'fiscalYear', 'purchaseOrder']);
        return view('warehouse.purchase-requests.show', compact('purchaseRequest'));
    }

    public function edit(PurchaseRequest $purchaseRequest)
    {
        Gate::authorize('access', 'purchase-requests.edit');
        $this->authorizeTenant($purchaseRequest);
        abort_unless($purchaseRequest->isEditable(), 403, 'این درخواست قابل ویرایش نیست.');
        $purchaseRequest->load('items');
        [$warehouses, $products, $units, $costCenters, $fiscalYears] = $this->formData();
        return view('warehouse.purchase-requests.edit', compact('purchaseRequest', 'warehouses', 'products', 'units', 'costCenters', 'fiscalYears'));
    }

    public function update(Request $request, PurchaseRequest $purchaseRequest)
    {
        Gate::authorize('access', 'purchase-requests.edit');
        $this->authorizeTenant($purchaseRequest);
        abort_unless($purchaseRequest->isEditable(), 403);

        $data = $request->validate([
            'warehouse_id'      => 'nullable|exists:warehouses,id',
            'fiscal_year_id'    => 'nullable|exists:fiscal_years,id',
            'cost_center_id'    => 'nullable|exists:cost_centers,id',
            'request_date'      => 'required|date',
            'required_by_date'  => 'nullable|date|after_or_equal:request_date',
            'priority'          => 'required|in:low,normal,high,urgent',
            'reason'            => 'nullable|string|max:1000',
            'notes'             => 'nullable|string|max:1000',
            'items'             => 'required|array|min:1',
            'items.*.product_id'=> 'required|exists:products,id',
            'items.*.quantity_requested' => 'required|numeric|min:0.001',
        ]);

        DB::transaction(function () use ($purchaseRequest, $data) {
            $purchaseRequest->update($data);
            $purchaseRequest->items()->delete();
            foreach ($data['items'] as $idx => $item) {
                $purchaseRequest->items()->create([
                    'product_id'           => $item['product_id'],
                    'measurement_unit_id'  => $item['measurement_unit_id'] ?? null,
                    'quantity_requested'   => $item['quantity_requested'],
                    'estimated_unit_price' => $item['estimated_unit_price'] ?? null,
                    'description'          => $item['description'] ?? null,
                    'sort_order'           => $idx,
                ]);
            }
        });

        return redirect()->route('warehouse.purchase-requests.show', $purchaseRequest)
            ->with('success', 'درخواست خرید ویرایش شد.');
    }

    public function destroy(PurchaseRequest $purchaseRequest)
    {
        Gate::authorize('access', 'purchase-requests.delete');
        $this->authorizeTenant($purchaseRequest);
        abort_unless($purchaseRequest->isEditable(), 403);
        $purchaseRequest->delete();
        return redirect()->route('warehouse.purchase-requests.index')->with('success', 'درخواست حذف شد.');
    }

    public function submit(PurchaseRequest $purchaseRequest)
    {
        Gate::authorize('access', 'purchase-requests.submit');
        $this->authorizeTenant($purchaseRequest);
        abort_unless($purchaseRequest->canSubmit(), 403, 'امکان ارسال این درخواست وجود ندارد.');
        $purchaseRequest->update(['status' => PurchaseRequest::STATUS_SUBMITTED, 'submitted_at' => now()]);
        return back()->with('success', 'درخواست برای بررسی ارسال شد.');
    }

    public function approve(Request $request, PurchaseRequest $purchaseRequest)
    {
        Gate::authorize('access', 'purchase-requests.approve');
        $this->authorizeTenant($purchaseRequest);
        abort_unless($purchaseRequest->canApprove(), 403);
        $purchaseRequest->update([
            'status'      => PurchaseRequest::STATUS_APPROVED,
            'approver_id' => auth()->id(),
            'approved_at' => now(),
        ]);
        return back()->with('success', 'درخواست تأیید شد.');
    }

    public function reject(Request $request, PurchaseRequest $purchaseRequest)
    {
        Gate::authorize('access', 'purchase-requests.approve');
        $this->authorizeTenant($purchaseRequest);
        abort_unless($purchaseRequest->canReject(), 403);
        $request->validate(['rejection_reason' => 'required|string|max:500']);
        $purchaseRequest->update([
            'status'           => PurchaseRequest::STATUS_REJECTED,
            'approver_id'      => auth()->id(),
            'rejected_at'      => now(),
            'rejection_reason' => $request->rejection_reason,
        ]);
        return back()->with('success', 'درخواست رد شد.');
    }

    public function convertToPo(Request $request, PurchaseRequest $purchaseRequest)
    {
        Gate::authorize('access', 'purchase-requests.convert');
        $this->authorizeTenant($purchaseRequest);
        abort_unless($purchaseRequest->canConvert(), 403);
        $request->validate([
            'supplier_id'  => 'nullable|exists:contacts,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'order_date'   => 'required|date',
        ]);

        $po = DB::transaction(function () use ($purchaseRequest, $request) {
            $tenantId  = $this->manager->getTenantId();
            $companyId = $this->manager->getCompanyId();

            $po = PurchaseOrder::create([
                'tenant_id'   => $tenantId,
                'company_id'  => $companyId,
                'po_number'   => 'PO-' . now()->format('Ym') . '-' . str_pad(
                    PurchaseOrder::where('tenant_id', $tenantId)->count() + 1, 5, '0', STR_PAD_LEFT
                ),
                'status'          => PurchaseOrder::STATUS_DRAFT,
                'supplier_id'     => $request->supplier_id,
                'warehouse_id'    => $request->warehouse_id,
                'fiscal_year_id'  => $purchaseRequest->fiscal_year_id,
                'cost_center_id'  => $purchaseRequest->cost_center_id,
                'order_date'      => $request->order_date,
                'reference_number'=> $purchaseRequest->pr_number,
                'notes'           => $purchaseRequest->notes,
                'created_by'      => auth()->id(),
            ]);

            foreach ($purchaseRequest->items as $idx => $item) {
                $po->items()->create([
                    'product_id'          => $item->product_id,
                    'measurement_unit_id' => $item->measurement_unit_id,
                    'quantity_ordered'    => $item->quantity_requested,
                    'unit_price'          => $item->estimated_unit_price,
                    'sort_order'          => $idx,
                ]);
            }

            $purchaseRequest->update([
                'status'          => PurchaseRequest::STATUS_CONVERTED,
                'purchase_order_id'=> $po->id,
                'converted_at'    => now(),
            ]);

            return $po;
        });

        return redirect()->route('warehouse.purchase-orders.show', $po)
            ->with('success', 'درخواست خرید با موفقیت به سفارش خرید تبدیل شد.');
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────
    private function ctx(): array
    {
        return [$this->manager->getTenantId(), $this->manager->getCompanyId()];
    }

    private function authorizeTenant(PurchaseRequest $pr): void
    {
        abort_unless($pr->tenant_id === $this->manager->getTenantId(), 403);
    }

    private function generateNumber(int $tenantId): string
    {
        $count = PurchaseRequest::where('tenant_id', $tenantId)->withTrashed()->count() + 1;
        return 'PR-' . now()->format('Ym') . '-' . str_pad($count, 5, '0', STR_PAD_LEFT);
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
        ];
    }
}
