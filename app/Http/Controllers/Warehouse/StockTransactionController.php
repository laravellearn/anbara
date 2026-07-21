<?php

namespace App\Http\Controllers\Warehouse;

use App\Enums\InventoryTransactionStatus;
use App\Models\CostCenter;
use App\Models\FiscalYear;
use App\Models\MeasurementUnit;
use App\Models\Product;
use App\Models\StockTransaction;
use App\Models\Warehouse;
use App\Models\WarehouseLocation;
use App\Http\Requests\Warehouse\StoreStockTransactionRequest;
use App\Http\Requests\Warehouse\UpdateStockTransactionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class StockTransactionController extends BaseController
{
    public function index(Request $request)
    {
        Gate::authorize('access', 'stock-transactions.view');

        $tenantId  = $this->manager->getTenantId();
        $companyId = $this->manager->getCompanyId();

        $query = StockTransaction::with(['product', 'warehouse', 'warehouseLocation', 'measurementUnit', 'user'])
            ->where('tenant_id', $tenantId)
            ->where('company_id', $companyId);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('product', fn($q) => $q->where('title', 'like', "%{$search}%")
                ->orWhere('sku', 'like', "%{$search}%"));
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $query->latest();
        $transactions = $query->paginate($request->per_page ?? 20);

        $warehouses = Warehouse::where('tenant_id', $tenantId)->where('is_active', true)->get();

        $stats = [
            'total'    => StockTransaction::where('tenant_id', $tenantId)->where('company_id', $companyId)->count(),
            'draft'    => StockTransaction::where('tenant_id', $tenantId)->where('company_id', $companyId)->where('status', InventoryTransactionStatus::DRAFT)->count(),
            'pending'  => StockTransaction::where('tenant_id', $tenantId)->where('company_id', $companyId)->where('status', InventoryTransactionStatus::PENDING)->count(),
            'approved' => StockTransaction::where('tenant_id', $tenantId)->where('company_id', $companyId)->where('status', InventoryTransactionStatus::APPROVED)->count(),
        ];

        if ($request->ajax() || $request->input('ajax')) {
            return response()->json([
                'html'      => view('warehouse.stock-transactions._table', compact('transactions'))->render(),
                'statsHtml' => view('warehouse.stock-transactions._stats', compact('stats'))->render(),
                'total'     => $transactions->total(),
            ]);
        }

        return view('warehouse.stock-transactions.index', compact('transactions', 'warehouses', 'stats'));
    }

    public function create()
    {
        Gate::authorize('access', 'stock-transactions.create');

        $tenantId  = $this->manager->getTenantId();
        $companyId = $this->manager->getCompanyId();

        $warehouses   = Warehouse::where('tenant_id', $tenantId)->where('is_active', true)->orderBy('title')->get();
        $products     = Product::where('tenant_id', $tenantId)->where('is_active', true)->orderBy('title')->get();
        $units        = MeasurementUnit::where('tenant_id', $tenantId)->where('is_active', true)->orderBy('title')->get();
        $costCenters  = CostCenter::where('tenant_id', $tenantId)->where('is_active', true)->orderBy('title')->get();
        $fiscalYears  = FiscalYear::where('tenant_id', $tenantId)->orderBy('start_date', 'desc')->get();
        $locations    = collect();

        return view('warehouse.stock-transactions.create', compact(
            'warehouses', 'products', 'units', 'costCenters', 'fiscalYears', 'locations'
        ));
    }

    public function store(StoreStockTransactionRequest $request)
    {
        Gate::authorize('access', 'stock-transactions.create');

        try {
            $data = $request->validated();
            $data['tenant_id']  = $this->manager->getTenantId();
            $data['company_id'] = $this->manager->getCompanyId();
            $data['user_id']    = auth()->id();
            $data['status']     = InventoryTransactionStatus::DRAFT->value;

            StockTransaction::create($data);

            return redirect()->route('warehouse.stock-transactions.index')->with('toast', [
                'message' => 'تراکنش انبار با موفقیت ثبت شد.',
                'type'    => 'success',
                'title'   => 'ثبت تراکنش',
            ]);
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'خطا در ثبت تراکنش: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function show(StockTransaction $stockTransaction)
    {
        Gate::authorize('access', 'stock-transactions.view');
        $this->authorizeTransaction($stockTransaction);

        $stockTransaction->load(['product', 'warehouse', 'warehouseLocation', 'measurementUnit', 'fiscalYear', 'costCenter', 'user']);

        return view('warehouse.stock-transactions.show', compact('stockTransaction'));
    }

    public function edit(StockTransaction $stockTransaction)
    {
        Gate::authorize('access', 'stock-transactions.edit');
        $this->authorizeTransaction($stockTransaction);

        // فقط تراکنش‌های Draft قابل ویرایش هستند
        if ($stockTransaction->status !== InventoryTransactionStatus::DRAFT) {
            return redirect()->route('warehouse.stock-transactions.index')->with('toast', [
                'message' => 'تنها تراکنش‌های پیش‌نویس قابل ویرایش هستند.',
                'type'    => 'warning',
                'title'   => 'ویرایش مجاز نیست',
            ]);
        }

        $tenantId    = $this->manager->getTenantId();
        $warehouses  = Warehouse::where('tenant_id', $tenantId)->where('is_active', true)->orderBy('title')->get();
        $products    = Product::where('tenant_id', $tenantId)->where('is_active', true)->orderBy('title')->get();
        $units       = MeasurementUnit::where('tenant_id', $tenantId)->where('is_active', true)->orderBy('title')->get();
        $costCenters = CostCenter::where('tenant_id', $tenantId)->where('is_active', true)->orderBy('title')->get();
        $fiscalYears = FiscalYear::where('tenant_id', $tenantId)->orderBy('start_date', 'desc')->get();
        $locations   = WarehouseLocation::where('warehouse_id', $stockTransaction->warehouse_id)->where('is_active', true)->orderBy('title')->get();

        return view('warehouse.stock-transactions.edit', compact(
            'stockTransaction', 'warehouses', 'products', 'units', 'costCenters', 'fiscalYears', 'locations'
        ));
    }

    public function update(UpdateStockTransactionRequest $request, StockTransaction $stockTransaction)
    {
        Gate::authorize('access', 'stock-transactions.edit');
        $this->authorizeTransaction($stockTransaction);

        if ($stockTransaction->status !== InventoryTransactionStatus::DRAFT) {
            return redirect()->route('warehouse.stock-transactions.index')->with('toast', [
                'message' => 'تنها تراکنش‌های پیش‌نویس قابل ویرایش هستند.',
                'type'    => 'warning',
                'title'   => 'ویرایش مجاز نیست',
            ]);
        }

        try {
            $stockTransaction->update($request->validated());

            return redirect()->route('warehouse.stock-transactions.index')->with('toast', [
                'message' => 'تراکنش با موفقیت ویرایش شد.',
                'type'    => 'success',
                'title'   => 'ویرایش تراکنش',
            ]);
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'خطا در ویرایش تراکنش: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function destroy(StockTransaction $stockTransaction)
    {
        Gate::authorize('access', 'stock-transactions.delete');
        $this->authorizeTransaction($stockTransaction);

        if ($stockTransaction->status !== InventoryTransactionStatus::DRAFT) {
            return redirect()->route('warehouse.stock-transactions.index')->with('toast', [
                'message' => 'تنها تراکنش‌های پیش‌نویس قابل حذف هستند.',
                'type'    => 'warning',
                'title'   => 'حذف مجاز نیست',
            ]);
        }

        try {
            $stockTransaction->delete();

            return redirect()->route('warehouse.stock-transactions.index')->with('toast', [
                'message' => 'تراکنش با موفقیت حذف شد.',
                'type'    => 'success',
                'title'   => 'حذف تراکنش',
            ]);
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'خطا در حذف تراکنش: ' . $e->getMessage()]);
        }
    }

    // ─── گردش‌کاری تأیید ─────────────────────────────────────────────────────

    /** ارسال برای تأیید (Draft → Pending) */
    public function submit(StockTransaction $stockTransaction)
    {
        Gate::authorize('access', 'stock-transactions.submit');
        $this->authorizeTransaction($stockTransaction);

        if ($stockTransaction->status !== InventoryTransactionStatus::DRAFT) {
            return redirect()->back()->with('toast', [
                'message' => 'تراکنش باید در حالت پیش‌نویس باشد.',
                'type'    => 'warning',
                'title'   => 'ارسال مجاز نیست',
            ]);
        }

        $stockTransaction->update(['status' => InventoryTransactionStatus::PENDING->value]);

        return redirect()->route('warehouse.stock-transactions.show', $stockTransaction)->with('toast', [
            'message' => 'تراکنش برای تأیید ارسال شد.',
            'type'    => 'info',
            'title'   => 'ارسال برای تأیید',
        ]);
    }

    /** تأیید (Pending → Approved) */
    public function approve(StockTransaction $stockTransaction)
    {
        Gate::authorize('access', 'stock-transactions.approve');
        $this->authorizeTransaction($stockTransaction);

        if ($stockTransaction->status !== InventoryTransactionStatus::PENDING) {
            return redirect()->back()->with('toast', [
                'message' => 'تنها تراکنش‌های در انتظار تأیید قابل تصویب هستند.',
                'type'    => 'warning',
                'title'   => 'تأیید مجاز نیست',
            ]);
        }

        $stockTransaction->update(['status' => InventoryTransactionStatus::APPROVED->value]);

        return redirect()->route('warehouse.stock-transactions.show', $stockTransaction)->with('toast', [
            'message' => 'تراکنش با موفقیت تأیید شد و موجودی انبار به‌روز گردید.',
            'type'    => 'success',
            'title'   => 'تأیید تراکنش',
        ]);
    }

    /** رد کردن (Pending → Rejected) */
    public function reject(StockTransaction $stockTransaction)
    {
        Gate::authorize('access', 'stock-transactions.approve');
        $this->authorizeTransaction($stockTransaction);

        if ($stockTransaction->status !== InventoryTransactionStatus::PENDING) {
            return redirect()->back()->with('toast', [
                'message' => 'تنها تراکنش‌های در انتظار تأیید قابل رد هستند.',
                'type'    => 'warning',
                'title'   => 'رد مجاز نیست',
            ]);
        }

        $stockTransaction->update(['status' => InventoryTransactionStatus::REJECTED->value]);

        return redirect()->route('warehouse.stock-transactions.show', $stockTransaction)->with('toast', [
            'message' => 'تراکنش رد شد.',
            'type'    => 'danger',
            'title'   => 'رد تراکنش',
        ]);
    }

    /** بارگذاری موقعیت‌های یک انبار (AJAX) */
    public function locations(Warehouse $warehouse)
    {
        $this->authorizeWarehouse($warehouse);
        $locations = WarehouseLocation::where('warehouse_id', $warehouse->id)
            ->where('is_active', true)
            ->orderBy('title')
            ->get(['id', 'title', 'code']);

        return response()->json($locations);
    }

    // ─── private helpers ──────────────────────────────────────────────────────

    private function authorizeTransaction(StockTransaction $tx): void
    {
        if ($tx->tenant_id !== $this->manager->getTenantId() || $tx->company_id !== $this->manager->getCompanyId()) {
            abort(403);
        }
    }

    private function authorizeWarehouse(Warehouse $warehouse): void
    {
        if ($warehouse->tenant_id !== $this->manager->getTenantId()) {
            abort(403);
        }
    }
}
