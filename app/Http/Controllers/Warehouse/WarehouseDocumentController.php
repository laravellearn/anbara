<?php

namespace App\Http\Controllers\Warehouse;

use App\Models\CostCenter;
use App\Models\FiscalYear;
use App\Models\MeasurementUnit;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\WarehouseDocument;
use App\Models\WarehouseDocumentItem;
use App\Models\WarehouseLocation;
use App\Http\Requests\Warehouse\StoreWarehouseDocumentRequest;
use App\Http\Requests\Warehouse\UpdateWarehouseDocumentRequest;
use App\Services\WarehouseDocumentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class WarehouseDocumentController extends BaseController
{
    public function __construct(
        \App\Services\TenantManager $manager,
        private WarehouseDocumentService $docService
    ) {
        parent::__construct($manager);
    }

    // ─── index ────────────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        Gate::authorize('access', 'warehouse-documents.view');

        $tenantId  = $this->manager->getTenantId();
        $companyId = $this->manager->getCompanyId();

        $query = WarehouseDocument::with(['warehouse', 'creator'])
            ->forTenant($tenantId, $companyId)
            ->latest('document_date');

        if ($request->filled('type'))         { $query->ofType($request->type); }
        if ($request->filled('status'))       { $query->withStatus($request->status); }
        if ($request->filled('warehouse_id')) { $query->where('warehouse_id', $request->warehouse_id); }
        if ($request->filled('date_from'))    { $query->whereDate('document_date', '>=', $request->date_from); }
        if ($request->filled('date_to'))      { $query->whereDate('document_date', '<=', $request->date_to); }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q
                ->where('document_number', 'like', "%{$s}%")
                ->orWhere('reference_number', 'like', "%{$s}%")
                ->orWhere('description', 'like', "%{$s}%")
            );
        }

        $documents  = $query->paginate($request->per_page ?? 20);
        $warehouses = Warehouse::where('tenant_id', $tenantId)->where('is_active', true)->get();

        $stats = [
            'total'    => WarehouseDocument::forTenant($tenantId, $companyId)->count(),
            'draft'    => WarehouseDocument::forTenant($tenantId, $companyId)->withStatus('draft')->count(),
            'pending'  => WarehouseDocument::forTenant($tenantId, $companyId)->withStatus('pending')->count(),
            'approved' => WarehouseDocument::forTenant($tenantId, $companyId)->withStatus('approved')->count(),
        ];

        if ($request->ajax() || $request->input('ajax')) {
            return response()->json([
                'html'      => view('warehouse.documents._table', compact('documents'))->render(),
                'statsHtml' => view('warehouse.documents._stats', compact('stats'))->render(),
                'total'     => $documents->total(),
            ]);
        }

        return view('warehouse.documents.index', compact('documents', 'warehouses', 'stats'));
    }

    // ─── create ───────────────────────────────────────────────────────────────
    public function create(Request $request)
    {
        Gate::authorize('access', 'warehouse-documents.create');
        [$warehouses, $products, $units, $costCenters, $fiscalYears, $contacts] = $this->formData();

        $defaultType = $request->type ?? WarehouseDocument::TYPE_RECEIPT;

        return view('warehouse.documents.create', compact(
            'warehouses', 'products', 'units', 'costCenters', 'fiscalYears', 'contacts', 'defaultType'
        ));
    }

    // ─── store ────────────────────────────────────────────────────────────────
    public function store(StoreWarehouseDocumentRequest $request)
    {
        Gate::authorize('access', 'warehouse-documents.create');

        try {
            DB::transaction(function () use ($request) {
                $tenantId  = $this->manager->getTenantId();
                $companyId = $this->manager->getCompanyId();

                $docData = array_merge($request->except('items'), [
                    'tenant_id'       => $tenantId,
                    'company_id'      => $companyId,
                    'created_by'      => auth()->id(),
                    'status'          => WarehouseDocument::STATUS_DRAFT,
                    'document_number' => $this->docService->generateDocumentNumber($request->type, $tenantId),
                ]);

                $doc = WarehouseDocument::create($docData);

                foreach ($request->items as $i => $item) {
                    $doc->items()->create(array_merge($item, ['sort_order' => $i]));
                }
            });

            return redirect()->route('warehouse.documents.index')->with('toast', [
                'message' => 'سند انبار با موفقیت ثبت شد.',
                'type'    => 'success',
                'title'   => 'ثبت سند',
            ]);
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'خطا در ثبت سند: ' . $e->getMessage()])
                ->withInput();
        }
    }

    // ─── show ─────────────────────────────────────────────────────────────────
    public function show(WarehouseDocument $document)
    {
        Gate::authorize('access', 'warehouse-documents.view');
        $this->authorizeDoc($document);

        $document->load(['items.product', 'items.measurementUnit', 'items.warehouseLocation',
            'warehouse', 'destinationWarehouse', 'warehouseLocation',
            'contact', 'fiscalYear', 'costCenter', 'creator', 'approver']);

        return view('warehouse.documents.show', compact('document'));
    }

    // ─── print ────────────────────────────────────────────────────────────────
    public function print(WarehouseDocument $document)
    {
        Gate::authorize('access', 'warehouse-documents.view');
        $this->authorizeDoc($document);

        $document->load(['items.product', 'items.measurementUnit',
            'warehouse', 'destinationWarehouse', 'contact', 'fiscalYear', 'costCenter', 'creator', 'approver']);

        return view('warehouse.documents.print', compact('document'));
    }

    // ─── edit ─────────────────────────────────────────────────────────────────
    public function edit(WarehouseDocument $document)
    {
        Gate::authorize('access', 'warehouse-documents.edit');
        $this->authorizeDoc($document);

        if (!$document->isEditable()) {
            return redirect()->route('warehouse.documents.show', $document)->with('toast', [
                'message' => 'فقط اسناد پیش‌نویس قابل ویرایش هستند.',
                'type'    => 'warning', 'title' => 'ویرایش مجاز نیست',
            ]);
        }

        $document->load('items');
        [$warehouses, $products, $units, $costCenters, $fiscalYears, $contacts] = $this->formData();

        return view('warehouse.documents.edit', compact(
            'document', 'warehouses', 'products', 'units', 'costCenters', 'fiscalYears', 'contacts'
        ));
    }

    // ─── update ───────────────────────────────────────────────────────────────
    public function update(UpdateWarehouseDocumentRequest $request, WarehouseDocument $document)
    {
        Gate::authorize('access', 'warehouse-documents.edit');
        $this->authorizeDoc($document);

        if (!$document->isEditable()) {
            return redirect()->route('warehouse.documents.show', $document)->with('toast', [
                'message' => 'فقط اسناد پیش‌نویس قابل ویرایش هستند.',
                'type'    => 'warning', 'title' => 'ویرایش مجاز نیست',
            ]);
        }

        try {
            DB::transaction(function () use ($request, $document) {
                $document->update($request->except('items'));
                $document->items()->delete();
                foreach ($request->items as $i => $item) {
                    $document->items()->create(array_merge($item, ['sort_order' => $i]));
                }
            });

            return redirect()->route('warehouse.documents.show', $document)->with('toast', [
                'message' => 'سند با موفقیت ویرایش شد.',
                'type'    => 'success', 'title' => 'ویرایش سند',
            ]);
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'خطا در ویرایش: ' . $e->getMessage()])
                ->withInput();
        }
    }

    // ─── destroy ──────────────────────────────────────────────────────────────
    public function destroy(WarehouseDocument $document)
    {
        Gate::authorize('access', 'warehouse-documents.delete');
        $this->authorizeDoc($document);

        if (!$document->isEditable()) {
            return redirect()->back()->with('toast', [
                'message' => 'فقط اسناد پیش‌نویس قابل حذف هستند.',
                'type'    => 'warning', 'title' => 'حذف مجاز نیست',
            ]);
        }

        try {
            $document->delete();
            return redirect()->route('warehouse.documents.index')->with('toast', [
                'message' => 'سند با موفقیت حذف شد.',
                'type'    => 'success', 'title' => 'حذف سند',
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    // ─── workflow ─────────────────────────────────────────────────────────────
    public function submit(WarehouseDocument $document)
    {
        Gate::authorize('access', 'warehouse-documents.submit');
        $this->authorizeDoc($document);

        if ($document->status !== WarehouseDocument::STATUS_DRAFT) {
            return redirect()->back()->with('toast', ['message' => 'سند باید در حالت پیش‌نویس باشد.', 'type' => 'warning', 'title' => '']);
        }

        $document->update(['status' => WarehouseDocument::STATUS_PENDING]);

        return redirect()->route('warehouse.documents.show', $document)->with('toast', [
            'message' => 'سند برای تأیید ارسال شد.', 'type' => 'info', 'title' => 'ارسال سند',
        ]);
    }

    public function approve(WarehouseDocument $document)
    {
        Gate::authorize('access', 'warehouse-documents.approve');
        $this->authorizeDoc($document);

        try {
            $this->docService->approve($document, auth()->id());

            return redirect()->route('warehouse.documents.show', $document)->with('toast', [
                'message' => 'سند تأیید شد و موجودی انبار به‌روز گردید.',
                'type'    => 'success', 'title' => 'تأیید سند',
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function reject(Request $request, WarehouseDocument $document)
    {
        Gate::authorize('access', 'warehouse-documents.approve');
        $this->authorizeDoc($document);

        try {
            $this->docService->reject($document, auth()->id(), $request->rejection_reason);

            return redirect()->route('warehouse.documents.show', $document)->with('toast', [
                'message' => 'سند رد شد.', 'type' => 'danger', 'title' => 'رد سند',
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function cancel(WarehouseDocument $document)
    {
        Gate::authorize('access', 'warehouse-documents.approve');
        $this->authorizeDoc($document);

        try {
            $this->docService->cancel($document);

            return redirect()->route('warehouse.documents.show', $document)->with('toast', [
                'message' => 'سند لغو شد و تراکنش‌های موجودی معکوس گردید.',
                'type'    => 'warning', 'title' => 'لغو سند',
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    // ─── helpers ──────────────────────────────────────────────────────────────
    private function authorizeDoc(WarehouseDocument $doc): void
    {
        if ($doc->tenant_id !== $this->manager->getTenantId() || $doc->company_id !== $this->manager->getCompanyId()) {
            abort(403);
        }
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
