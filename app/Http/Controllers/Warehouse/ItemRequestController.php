<?php

namespace App\Http\Controllers\Warehouse;

use App\Models\CostCenter;
use App\Models\FiscalYear;
use App\Models\ItemRequest;
use App\Models\MeasurementUnit;
use App\Models\OrganizationalUnit;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\WarehouseDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class ItemRequestController extends BaseController
{
    public function __construct(\App\Services\TenantManager $manager)
    {
        parent::__construct($manager);
    }

    public function index(Request $request)
    {
        Gate::authorize('access', 'item-requests.view');
        [$tenantId, $companyId] = $this->ctx();

        $query = ItemRequest::with(['requester', 'warehouse', 'organizationalUnit'])
            ->forTenant($tenantId, $companyId)
            ->withCount('items')
            ->latest('request_date');

        if ($request->filled('status'))     { $query->where('status', $request->status); }
        if ($request->filled('priority'))   { $query->where('priority', $request->priority); }
        if ($request->filled('warehouse_id')){ $query->where('warehouse_id', $request->warehouse_id); }
        if ($request->filled('date_from'))  { $query->whereDate('request_date', '>=', $request->date_from); }
        if ($request->filled('date_to'))    { $query->whereDate('request_date', '<=', $request->date_to); }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('ir_number', 'like', "%$s%")->orWhere('purpose', 'like', "%$s%"));
        }

        $itemRequests = $query->paginate($request->per_page ?? 20)->withQueryString();
        $warehouses   = Warehouse::where('tenant_id', $tenantId)->where('is_active', true)->orderBy('title')->get();

        $stats = [
            'total'     => ItemRequest::forTenant($tenantId, $companyId)->count(),
            'draft'     => ItemRequest::forTenant($tenantId, $companyId)->where('status', 'draft')->count(),
            'submitted' => ItemRequest::forTenant($tenantId, $companyId)->where('status', 'submitted')->count(),
            'approved'  => ItemRequest::forTenant($tenantId, $companyId)->where('status', 'approved')->count(),
        ];

        return view('warehouse.item-requests.index', compact('itemRequests', 'warehouses', 'stats'));
    }

    public function create()
    {
        Gate::authorize('access', 'item-requests.create');
        [$warehouses, $products, $units, $costCenters, $fiscalYears, $orgUnits] = $this->formData();
        $itemRequest = new ItemRequest();
        return view('warehouse.item-requests.create', compact('warehouses', 'products', 'units', 'costCenters', 'fiscalYears', 'orgUnits', 'itemRequest'));
    }

    public function store(Request $request)
    {
        Gate::authorize('access', 'item-requests.create');
        $data = $request->validate([
            'warehouse_id'                => 'required|exists:warehouses,id',
            'organizational_unit_id'      => 'nullable|exists:organizational_units,id',
            'fiscal_year_id'              => 'nullable|exists:fiscal_years,id',
            'cost_center_id'              => 'nullable|exists:cost_centers,id',
            'request_date'                => 'required|date',
            'required_by_date'            => 'nullable|date|after_or_equal:request_date',
            'priority'                    => 'required|in:low,normal,high,urgent',
            'purpose'                     => 'nullable|string|max:1000',
            'notes'                       => 'nullable|string|max:1000',
            'items'                       => 'required|array|min:1',
            'items.*.product_id'          => 'required|exists:products,id',
            'items.*.quantity_requested'  => 'required|numeric|min:0.001',
        ]);

        DB::transaction(function () use ($data) {
            $tenantId  = $this->manager->getTenantId();
            $companyId = $this->manager->getCompanyId();

            $ir = ItemRequest::create([
                ...$data,
                'tenant_id'    => $tenantId,
                'company_id'   => $companyId,
                'ir_number'    => $this->generateNumber($tenantId),
                'requester_id' => auth()->id(),
                'created_by'   => auth()->id(),
            ]);

            foreach ($data['items'] as $idx => $item) {
                $ir->items()->create([
                    'product_id'          => $item['product_id'],
                    'measurement_unit_id' => $item['measurement_unit_id'] ?? null,
                    'quantity_requested'  => $item['quantity_requested'],
                    'description'         => $item['description'] ?? null,
                    'sort_order'          => $idx,
                ]);
            }
        });

        return redirect()->route('warehouse.item-requests.index')->with('success', 'درخواست کالا ثبت شد.');
    }

    public function show(ItemRequest $itemRequest)
    {
        Gate::authorize('access', 'item-requests.view');
        $this->authorizeTenant($itemRequest);
        $itemRequest->load(['items.product', 'items.measurementUnit', 'requester', 'approver', 'warehouse', 'organizationalUnit', 'costCenter', 'fiscalYear', 'warehouseDocument']);
        return view('warehouse.item-requests.show', compact('itemRequest'));
    }

    public function edit(ItemRequest $itemRequest)
    {
        Gate::authorize('access', 'item-requests.edit');
        $this->authorizeTenant($itemRequest);
        abort_unless($itemRequest->isEditable(), 403, 'این درخواست قابل ویرایش نیست.');
        $itemRequest->load('items');
        [$warehouses, $products, $units, $costCenters, $fiscalYears, $orgUnits] = $this->formData();
        return view('warehouse.item-requests.edit', compact('itemRequest', 'warehouses', 'products', 'units', 'costCenters', 'fiscalYears', 'orgUnits'));
    }

    public function update(Request $request, ItemRequest $itemRequest)
    {
        Gate::authorize('access', 'item-requests.edit');
        $this->authorizeTenant($itemRequest);
        abort_unless($itemRequest->isEditable(), 403);

        $data = $request->validate([
            'warehouse_id'               => 'required|exists:warehouses,id',
            'organizational_unit_id'     => 'nullable|exists:organizational_units,id',
            'fiscal_year_id'             => 'nullable|exists:fiscal_years,id',
            'cost_center_id'             => 'nullable|exists:cost_centers,id',
            'request_date'               => 'required|date',
            'required_by_date'           => 'nullable|date|after_or_equal:request_date',
            'priority'                   => 'required|in:low,normal,high,urgent',
            'purpose'                    => 'nullable|string|max:1000',
            'notes'                      => 'nullable|string|max:1000',
            'items'                      => 'required|array|min:1',
            'items.*.product_id'         => 'required|exists:products,id',
            'items.*.quantity_requested' => 'required|numeric|min:0.001',
        ]);

        DB::transaction(function () use ($itemRequest, $data) {
            $itemRequest->update($data);
            $itemRequest->items()->delete();
            foreach ($data['items'] as $idx => $item) {
                $itemRequest->items()->create([
                    'product_id'          => $item['product_id'],
                    'measurement_unit_id' => $item['measurement_unit_id'] ?? null,
                    'quantity_requested'  => $item['quantity_requested'],
                    'description'         => $item['description'] ?? null,
                    'sort_order'          => $idx,
                ]);
            }
        });

        return redirect()->route('warehouse.item-requests.show', $itemRequest)->with('success', 'درخواست کالا ویرایش شد.');
    }

    public function destroy(ItemRequest $itemRequest)
    {
        Gate::authorize('access', 'item-requests.delete');
        $this->authorizeTenant($itemRequest);
        abort_unless($itemRequest->isEditable(), 403);
        $itemRequest->delete();
        return redirect()->route('warehouse.item-requests.index')->with('success', 'درخواست حذف شد.');
    }

    public function submit(ItemRequest $itemRequest)
    {
        Gate::authorize('access', 'item-requests.submit');
        $this->authorizeTenant($itemRequest);
        abort_unless($itemRequest->canSubmit(), 403);
        $itemRequest->update(['status' => ItemRequest::STATUS_SUBMITTED, 'submitted_at' => now()]);
        return back()->with('success', 'درخواست برای بررسی ارسال شد.');
    }

    public function approve(Request $request, ItemRequest $itemRequest)
    {
        Gate::authorize('access', 'item-requests.approve');
        $this->authorizeTenant($itemRequest);
        abort_unless($itemRequest->canApprove(), 403);
        $itemRequest->update([
            'status'      => ItemRequest::STATUS_APPROVED,
            'approver_id' => auth()->id(),
            'approved_at' => now(),
        ]);
        return back()->with('success', 'درخواست کالا تأیید شد.');
    }

    public function reject(Request $request, ItemRequest $itemRequest)
    {
        Gate::authorize('access', 'item-requests.approve');
        $this->authorizeTenant($itemRequest);
        abort_unless($itemRequest->canReject(), 403);
        $request->validate(['rejection_reason' => 'required|string|max:500']);
        $itemRequest->update([
            'status'           => ItemRequest::STATUS_REJECTED,
            'approver_id'      => auth()->id(),
            'rejected_at'      => now(),
            'rejection_reason' => $request->rejection_reason,
        ]);
        return back()->with('success', 'درخواست رد شد.');
    }

    public function issueDocument(ItemRequest $itemRequest)
    {
        Gate::authorize('access', 'item-requests.dispatch');
        $this->authorizeTenant($itemRequest);
        abort_unless($itemRequest->canIssue(), 403);

        $doc = DB::transaction(function () use ($itemRequest) {
            $doc = WarehouseDocument::create([
                'tenant_id'       => $itemRequest->tenant_id,
                'company_id'      => $itemRequest->company_id,
                'document_number' => 'ISS-' . now()->format('Ym') . '-' . str_pad(
                    WarehouseDocument::where('tenant_id', $itemRequest->tenant_id)->count() + 1, 5, '0', STR_PAD_LEFT
                ),
                'type'            => WarehouseDocument::TYPE_ISSUE,
                'status'          => WarehouseDocument::STATUS_PENDING,
                'warehouse_id'    => $itemRequest->warehouse_id,
                'fiscal_year_id'  => $itemRequest->fiscal_year_id,
                'cost_center_id'  => $itemRequest->cost_center_id,
                'document_date'   => now()->toDateString(),
                'reference_number'=> $itemRequest->ir_number,
                'description'     => "حواله برای درخواست {$itemRequest->ir_number}",
                'created_by'      => auth()->id(),
            ]);

            foreach ($itemRequest->items as $idx => $item) {
                $doc->items()->create([
                    'product_id'          => $item->product_id,
                    'measurement_unit_id' => $item->measurement_unit_id,
                    'quantity'            => $item->quantity_requested,
                    'sort_order'          => $idx,
                ]);
                $item->update(['quantity_issued' => $item->quantity_requested]);
            }

            $itemRequest->update([
                'status'               => ItemRequest::STATUS_ISSUED,
                'warehouse_document_id'=> $doc->id,
                'issued_at'            => now(),
            ]);

            return $doc;
        });

        return redirect()->route('warehouse.documents.show', $doc)
            ->with('success', 'حواله انبار برای این درخواست ایجاد شد.');
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────
    private function ctx(): array
    {
        return [$this->manager->getTenantId(), $this->manager->getCompanyId()];
    }

    private function authorizeTenant(ItemRequest $ir): void
    {
        abort_unless($ir->tenant_id === $this->manager->getTenantId(), 403);
    }

    private function generateNumber(int $tenantId): string
    {
        $count = ItemRequest::where('tenant_id', $tenantId)->withTrashed()->count() + 1;
        return 'IR-' . now()->format('Ym') . '-' . str_pad($count, 5, '0', STR_PAD_LEFT);
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
            OrganizationalUnit::where('tenant_id', $tid)->orderBy('title')->get(),
        ];
    }
}
