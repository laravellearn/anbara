<?php

namespace App\Http\Controllers\Warehouse;

use App\Models\Product;
use App\Models\StockTransaction;
use App\Models\Warehouse;
use App\Models\WarehouseDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class ReportController extends BaseController
{
    // ─── ۱. گزارش موجودی لحظه‌ای ─────────────────────────────────────────────
    public function inventory(Request $request)
    {
        Gate::authorize('access', 'reports.inventory');

        [$tenantId, $companyId] = $this->tenantCtx();

        $query = DB::table('stock_transactions as st')
            ->join('products as p',    'p.id',  '=', 'st.product_id')
            ->join('warehouses as wh', 'wh.id', '=', 'st.warehouse_id')
            ->leftJoin('measurement_units as mu', 'mu.id', '=', 'st.measurement_unit_id')
            ->leftJoin('categories as cat', 'cat.id', '=', 'p.category_id')
            ->where('st.tenant_id',   $tenantId)
            ->where('st.company_id',  $companyId)
            ->where('st.status',      'approved')
            ->groupBy('p.id','p.title','p.sku','p.minimum_stock','wh.id','wh.title','mu.title','cat.title')
            ->select([
                'p.id as product_id',
                'p.title as product_title',
                'p.sku',
                'p.minimum_stock',
                'cat.title as category',
                'wh.id as warehouse_id',
                'wh.title as warehouse_title',
                'mu.title as unit',
                DB::raw("SUM(CASE WHEN st.type IN ('purchase','return_sale','opening','transfer_in','adjustment_in','asset_return') THEN st.quantity ELSE 0 END)
                       - SUM(CASE WHEN st.type IN ('sale','return_purchase','transfer_out','adjustment_out','scrap','asset_assign') THEN st.quantity ELSE 0 END) AS current_stock"),
            ]);

        if ($request->filled('warehouse_id')) {
            $query->where('st.warehouse_id', $request->warehouse_id);
        }
        if ($request->filled('category_id')) {
            $query->where('p.category_id', $request->category_id);
        }
        if ($request->filled('product_search')) {
            $s = $request->product_search;
            $query->where(fn($q) => $q->where('p.title','like',"%{$s}%")->orWhere('p.sku','like',"%{$s}%"));
        }
        if ($request->filled('zero_stock') && $request->zero_stock === 'hide') {
            $query->havingRaw('current_stock > 0');
        }
        if ($request->filled('below_min') && $request->below_min === '1') {
            $query->havingRaw('current_stock < p.minimum_stock AND p.minimum_stock > 0');
        }

        $rows       = $query->orderBy('p.title')->paginate(50)->withQueryString();
        $warehouses = Warehouse::where('tenant_id', $tenantId)->where('is_active', true)->get();
        $categories = DB::table('categories')->where('tenant_id', $tenantId)->orderBy('title')->get();

        $summary = [
            'total_products'  => $query->clone()->count(DB::raw('DISTINCT p.id')),
            'below_min_count' => $query->clone()->havingRaw('current_stock < p.minimum_stock AND p.minimum_stock > 0')->count(),
        ];

        if ($request->input('export') === 'excel') {
            return $this->exportExcel($rows->getQuery()->get(), 'inventory', 'گزارش موجودی لحظه‌ای');
        }

        if ($request->input('export') === 'pdf') {
            return $this->exportInventoryPdf($request, $tenantId, $companyId);
        }

        return view('warehouse.reports.inventory', compact('rows','warehouses','categories','summary'));
    }

    // ─── PDF موجودی لحظه‌ای ───────────────────────────────────────────────────
    public function inventoryPdf(Request $request)
    {
        Gate::authorize('access', 'reports.inventory');
        [$tenantId, $companyId] = $this->tenantCtx();
        return $this->exportInventoryPdf($request, $tenantId, $companyId);
    }

    private function exportInventoryPdf(Request $request, int $tenantId, int $companyId): \Illuminate\View\View
    {
        $query = DB::table('stock_transactions as st')
            ->join('products as p',    'p.id',  '=', 'st.product_id')
            ->join('warehouses as wh', 'wh.id', '=', 'st.warehouse_id')
            ->leftJoin('measurement_units as mu', 'mu.id', '=', 'st.measurement_unit_id')
            ->leftJoin('categories as cat', 'cat.id', '=', 'p.category_id')
            ->where('st.tenant_id',   $tenantId)
            ->where('st.company_id',  $companyId)
            ->where('st.status',      'approved')
            ->groupBy('p.id','p.title','p.sku','p.minimum_stock','wh.id','wh.title','mu.title','cat.title')
            ->select([
                'p.title as product_title', 'p.sku', 'p.minimum_stock',
                'cat.title as category', 'wh.title as warehouse_title', 'mu.title as unit',
                DB::raw("SUM(CASE WHEN st.type IN ('purchase','return_sale','opening','transfer_in','adjustment_in','asset_return') THEN st.quantity ELSE 0 END)
                       - SUM(CASE WHEN st.type IN ('sale','return_purchase','transfer_out','adjustment_out','scrap','asset_assign') THEN st.quantity ELSE 0 END) AS current_stock"),
            ]);

        if ($request->filled('warehouse_id')) { $query->where('st.warehouse_id', $request->warehouse_id); }
        if ($request->filled('category_id'))  { $query->where('p.category_id', $request->category_id); }
        if ($request->filled('product_search')) {
            $s = $request->product_search;
            $query->where(fn($q) => $q->where('p.title','like',"%{$s}%")->orWhere('p.sku','like',"%{$s}%"));
        }

        $rows             = $query->orderBy('p.title')->get();
        $tenant           = $this->manager->getTenant();
        $selectedWarehouse = $request->filled('warehouse_id')
            ? \App\Models\Warehouse::find($request->warehouse_id) : null;
        $selectedCategory  = $request->filled('category_id')
            ? DB::table('categories')->find($request->category_id) : null;

        return view('warehouse.reports.inventory-pdf', compact('rows','tenant','selectedWarehouse','selectedCategory'));
    }

    // ─── ۲. کارتکس کالا ──────────────────────────────────────────────────────
    public function ledger(Request $request)
    {
        Gate::authorize('access', 'reports.ledger');

        [$tenantId, $companyId] = $this->tenantCtx();

        $products   = Product::where('tenant_id', $tenantId)->where('is_active', true)->orderBy('title')->get();
        $warehouses = Warehouse::where('tenant_id', $tenantId)->where('is_active', true)->get();

        $rows        = collect();
        $product     = null;
        $openingBal  = 0;
        $closingBal  = 0;

        if ($request->filled('product_id')) {
            $product = Product::where('tenant_id', $tenantId)->findOrFail($request->product_id);

            // موجودی افتتاحی (تا قبل از date_from)
            if ($request->filled('date_from')) {
                $openingBal = $this->calcBalance($tenantId, $companyId, $request->product_id,
                    $request->warehouse_id ?? null, null, $request->date_from, exclusive: true);
            }

            $txQuery = DB::table('stock_transactions as st')
                ->join('warehouses as wh', 'wh.id', '=', 'st.warehouse_id')
                ->leftJoin('users as u', 'u.id', '=', 'st.user_id')
                ->where('st.tenant_id',  $tenantId)
                ->where('st.company_id', $companyId)
                ->where('st.status',     'approved')
                ->where('st.product_id', $request->product_id)
                ->select([
                    'st.id', 'st.type', 'st.quantity', 'st.unit_price', 'st.description',
                    'st.created_at', 'wh.title as warehouse_title', 'u.name as user_name',
                    DB::raw("CASE WHEN st.type IN ('purchase','return_sale','opening','transfer_in','adjustment_in','asset_return') THEN st.quantity ELSE 0 END as qty_in"),
                    DB::raw("CASE WHEN st.type IN ('sale','return_purchase','transfer_out','adjustment_out','scrap','asset_assign') THEN st.quantity ELSE 0 END as qty_out"),
                ])
                ->orderBy('st.created_at');

            if ($request->filled('warehouse_id')) {
                $txQuery->where('st.warehouse_id', $request->warehouse_id);
            }
            if ($request->filled('date_from')) {
                $txQuery->whereDate('st.created_at', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $txQuery->whereDate('st.created_at', '<=', $request->date_to);
            }

            $rows = $txQuery->get();

            // محاسبه تراز در هر ردیف
            $running = $openingBal;
            $rows = $rows->map(function($r) use (&$running) {
                $running += $r->qty_in - $r->qty_out;
                $r->balance = $running;
                return $r;
            });

            $closingBal = $running;

            if ($request->input('export') === 'excel') {
                return $this->exportExcel($rows, 'ledger', 'کارتکس کالا — ' . $product->title);
            }
        }

        return view('warehouse.reports.ledger', compact(
            'products', 'warehouses', 'rows', 'product', 'openingBal', 'closingBal'
        ));
    }

    // ─── ۳. خلاصه ورود و خروج ────────────────────────────────────────────────
    public function inOutSummary(Request $request)
    {
        Gate::authorize('access', 'reports.summary');

        [$tenantId, $companyId] = $this->tenantCtx();

        $warehouses = Warehouse::where('tenant_id', $tenantId)->where('is_active', true)->get();
        $rows       = collect();

        $dateFrom = $request->date_from ?? now()->startOfMonth()->format('Y-m-d');
        $dateTo   = $request->date_to   ?? now()->format('Y-m-d');

        $query = DB::table('stock_transactions as st')
            ->join('products as p', 'p.id', '=', 'st.product_id')
            ->leftJoin('measurement_units as mu', 'mu.id', '=', 'st.measurement_unit_id')
            ->leftJoin('categories as cat', 'cat.id', '=', 'p.category_id')
            ->where('st.tenant_id',  $tenantId)
            ->where('st.company_id', $companyId)
            ->where('st.status',     'approved')
            ->whereDate('st.created_at', '>=', $dateFrom)
            ->whereDate('st.created_at', '<=', $dateTo)
            ->groupBy('p.id','p.title','p.sku','mu.title','cat.title')
            ->select([
                'p.id as product_id',
                'p.title as product_title',
                'p.sku',
                'cat.title as category',
                'mu.title as unit',
                DB::raw("SUM(CASE WHEN st.type IN ('purchase','return_sale','opening','transfer_in','adjustment_in','asset_return') THEN st.quantity ELSE 0 END) as total_in"),
                DB::raw("SUM(CASE WHEN st.type IN ('sale','return_purchase','transfer_out','adjustment_out','scrap','asset_assign') THEN st.quantity ELSE 0 END) as total_out"),
                DB::raw("SUM(CASE WHEN st.type IN ('purchase','return_sale','opening','transfer_in','adjustment_in','asset_return') THEN st.quantity * COALESCE(st.unit_price,0) ELSE 0 END) as value_in"),
                DB::raw("SUM(CASE WHEN st.type IN ('sale','return_purchase','transfer_out','adjustment_out','scrap','asset_assign') THEN st.quantity * COALESCE(st.unit_price,0) ELSE 0 END) as value_out"),
            ]);

        if ($request->filled('warehouse_id')) {
            $query->where('st.warehouse_id', $request->warehouse_id);
        }
        if ($request->filled('category_id')) {
            $query->where('p.category_id', $request->category_id);
        }

        $rows = $query->orderBy('p.title')->get();

        $totals = [
            'total_in'   => $rows->sum('total_in'),
            'total_out'  => $rows->sum('total_out'),
            'value_in'   => $rows->sum('value_in'),
            'value_out'  => $rows->sum('value_out'),
        ];

        $categories = DB::table('categories')->where('tenant_id', $tenantId)->orderBy('title')->get();

        if ($request->input('export') === 'excel') {
            return $this->exportExcel($rows, 'in-out-summary', 'خلاصه ورود و خروج');
        }

        return view('warehouse.reports.in-out-summary', compact('rows','warehouses','categories','totals','dateFrom','dateTo'));
    }

    // ─── ۴. گزارش زیر حداقل موجودی ──────────────────────────────────────────
    public function belowMinimum(Request $request)
    {
        Gate::authorize('access', 'reports.inventory');

        [$tenantId, $companyId] = $this->tenantCtx();

        $warehouses = Warehouse::where('tenant_id', $tenantId)->where('is_active', true)->get();

        $rows = DB::table('stock_transactions as st')
            ->join('products as p',    'p.id',  '=', 'st.product_id')
            ->join('warehouses as wh', 'wh.id', '=', 'st.warehouse_id')
            ->leftJoin('measurement_units as mu', 'mu.id', '=', 'st.measurement_unit_id')
            ->leftJoin('categories as cat', 'cat.id', '=', 'p.category_id')
            ->where('st.tenant_id',  $tenantId)
            ->where('st.company_id', $companyId)
            ->where('st.status',     'approved')
            ->where('p.minimum_stock', '>', 0)
            ->when($request->warehouse_id, fn($q, $v) => $q->where('st.warehouse_id', $v))
            ->groupBy('p.id','p.title','p.sku','p.minimum_stock','wh.id','wh.title','mu.title','cat.title')
            ->select([
                'p.id as product_id', 'p.title as product_title', 'p.sku',
                'p.minimum_stock', 'cat.title as category',
                'wh.id as warehouse_id', 'wh.title as warehouse_title', 'mu.title as unit',
                DB::raw("SUM(CASE WHEN st.type IN ('purchase','return_sale','opening','transfer_in','adjustment_in','asset_return') THEN st.quantity ELSE 0 END)
                       - SUM(CASE WHEN st.type IN ('sale','return_purchase','transfer_out','adjustment_out','scrap','asset_assign') THEN st.quantity ELSE 0 END) AS current_stock"),
            ])
            ->havingRaw('current_stock < p.minimum_stock')
            ->orderByRaw('(p.minimum_stock - current_stock) DESC')
            ->get();

        if ($request->input('export') === 'excel') {
            return $this->exportExcel($rows, 'below-minimum', 'کالاهای زیر حداقل موجودی');
        }

        return view('warehouse.reports.below-minimum', compact('rows','warehouses'));
    }

    // ─── ۵. گزارش ارزش موجودی ────────────────────────────────────────────────
    public function stockValue(Request $request)
    {
        Gate::authorize('access', 'reports.inventory');

        [$tenantId, $companyId] = $this->tenantCtx();

        $warehouses = Warehouse::where('tenant_id', $tenantId)->where('is_active', true)->get();
        $categories = DB::table('categories')->where('tenant_id', $tenantId)->orderBy('title')->get();

        $query = DB::table('stock_transactions as st')
            ->join('products as p',    'p.id',  '=', 'st.product_id')
            ->join('warehouses as wh', 'wh.id', '=', 'st.warehouse_id')
            ->leftJoin('measurement_units as mu', 'mu.id', '=', 'st.measurement_unit_id')
            ->leftJoin('categories as cat', 'cat.id', '=', 'p.category_id')
            ->where('st.tenant_id',  $tenantId)
            ->where('st.company_id', $companyId)
            ->where('st.status',     'approved')
            ->groupBy('p.id','p.title','p.sku','wh.id','wh.title','mu.title','cat.title')
            ->select([
                'p.id as product_id', 'p.title as product_title', 'p.sku',
                'cat.title as category', 'wh.title as warehouse_title', 'mu.title as unit',
                DB::raw("SUM(CASE WHEN st.type IN ('purchase','return_sale','opening','transfer_in','adjustment_in','asset_return') THEN st.quantity ELSE 0 END)
                       - SUM(CASE WHEN st.type IN ('sale','return_purchase','transfer_out','adjustment_out','scrap','asset_assign') THEN st.quantity ELSE 0 END) AS current_stock"),
                DB::raw("MAX(st.unit_price) as last_unit_price"),
                DB::raw("(SUM(CASE WHEN st.type IN ('purchase','return_sale','opening','transfer_in','adjustment_in','asset_return') THEN st.quantity ELSE 0 END)
                        - SUM(CASE WHEN st.type IN ('sale','return_purchase','transfer_out','adjustment_out','scrap','asset_assign') THEN st.quantity ELSE 0 END))
                       * MAX(COALESCE(st.unit_price,0)) AS stock_value"),
            ]);

        if ($request->filled('warehouse_id')) {
            $query->where('st.warehouse_id', $request->warehouse_id);
        }
        if ($request->filled('category_id')) {
            $query->where('p.category_id', $request->category_id);
        }

        $rows       = $query->havingRaw('current_stock > 0')->orderByRaw('stock_value DESC')->get();
        $totalValue = $rows->sum('stock_value');

        if ($request->input('export') === 'excel') {
            return $this->exportExcel($rows, 'stock-value', 'گزارش ارزش موجودی');
        }

        return view('warehouse.reports.stock-value', compact('rows','warehouses','categories','totalValue'));
    }

    // ─── ۶. خلاصه خرید (PR → PO → Invoice) ──────────────────────────────────
    public function purchaseSummary(Request $request)
    {
        Gate::authorize('access', 'reports.purchase');

        [$tenantId, $companyId] = $this->tenantCtx();

        $dateFrom = $request->date_from ?? now()->startOfMonth()->format('Y-m-d');
        $dateTo   = $request->date_to   ?? now()->format('Y-m-d');

        // آمار درخواست‌های خرید
        $prStats = DB::table('purchase_requests')
            ->where('tenant_id', $tenantId)
            ->where('company_id', $companyId)
            ->whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo)
            ->selectRaw('status, COUNT(*) as cnt, SUM(total_amount) as total')
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        // آمار سفارشات خرید
        $poStats = DB::table('purchase_orders')
            ->where('tenant_id', $tenantId)
            ->where('company_id', $companyId)
            ->whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo)
            ->selectRaw('status, COUNT(*) as cnt, SUM(total_amount) as total')
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        // آمار فاکتورها
        $invoiceStats = DB::table('purchase_invoices')
            ->where('tenant_id', $tenantId)
            ->where('company_id', $companyId)
            ->whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo)
            ->selectRaw('status, COUNT(*) as cnt, SUM(total_amount) as total')
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        // جدول ریز فاکتورها
        $invoices = DB::table('purchase_invoices as pi')
            ->leftJoin('contacts as c', 'c.id', '=', 'pi.supplier_id')
            ->leftJoin('purchase_orders as po', 'po.id', '=', 'pi.purchase_order_id')
            ->where('pi.tenant_id',  $tenantId)
            ->where('pi.company_id', $companyId)
            ->whereDate('pi.created_at', '>=', $dateFrom)
            ->whereDate('pi.created_at', '<=', $dateTo)
            ->select([
                'pi.id', 'pi.invoice_number', 'pi.invoice_date', 'pi.status',
                'pi.total_amount', 'pi.paid_amount',
                'c.name as supplier_name',
                'po.po_number',
            ])
            ->orderByDesc('pi.invoice_date')
            ->get();

        $totals = [
            'pr_total'      => $prStats->sum('total'),
            'pr_count'      => $prStats->sum('cnt'),
            'po_total'      => $poStats->sum('total'),
            'po_count'      => $poStats->sum('cnt'),
            'inv_total'     => $invoiceStats->sum('total'),
            'inv_count'     => $invoiceStats->sum('cnt'),
            'inv_paid'      => $invoices->sum('paid_amount'),
            'inv_unpaid'    => $invoices->sum('total_amount') - $invoices->sum('paid_amount'),
        ];

        if ($request->input('export') === 'excel') {
            return $this->exportExcel($invoices, 'purchase-summary', 'خلاصه خرید');
        }

        return view('warehouse.reports.purchase-summary', compact(
            'prStats','poStats','invoiceStats','invoices','totals','dateFrom','dateTo'
        ));
    }

    // ─── ۷. عملکرد تامین‌کنندگان ─────────────────────────────────────────────
    public function supplierPerformance(Request $request)
    {
        Gate::authorize('access', 'reports.purchase');

        [$tenantId, $companyId] = $this->tenantCtx();

        $dateFrom = $request->date_from ?? now()->subMonths(3)->format('Y-m-d');
        $dateTo   = $request->date_to   ?? now()->format('Y-m-d');

        // عملکرد هر تامین‌کننده: تعداد سفارش، جمع ارزش، میانگین تاخیر تحویل، تعداد رد شده
        $rows = DB::table('purchase_orders as po')
            ->join('contacts as c', 'c.id', '=', 'po.supplier_id')
            ->where('po.tenant_id',  $tenantId)
            ->where('po.company_id', $companyId)
            ->whereDate('po.created_at', '>=', $dateFrom)
            ->whereDate('po.created_at', '<=', $dateTo)
            ->groupBy('c.id', 'c.name', 'c.mobile')
            ->select([
                'c.id as supplier_id',
                'c.name as supplier_name',
                'c.mobile',
                DB::raw('COUNT(po.id) as total_orders'),
                DB::raw('SUM(po.total_amount) as total_value'),
                DB::raw("SUM(CASE WHEN po.status='received' THEN 1 ELSE 0 END) as completed_orders"),
                DB::raw("SUM(CASE WHEN po.status='cancelled' THEN 1 ELSE 0 END) as cancelled_orders"),
                DB::raw("AVG(CASE WHEN po.status='received' AND po.delivery_date IS NOT NULL
                              THEN DATEDIFF(po.updated_at, po.delivery_date) ELSE NULL END) as avg_delay_days"),
            ])
            ->orderByDesc('total_value')
            ->get()
            ->map(function ($row) {
                $row->completion_rate = $row->total_orders > 0
                    ? round(($row->completed_orders / $row->total_orders) * 100, 1)
                    : 0;
                return $row;
            });

        // فاکتورهای پرداخت‌نشده به تفکیک تامین‌کننده
        $unpaidBySupplier = DB::table('purchase_invoices')
            ->where('tenant_id',  $tenantId)
            ->where('company_id', $companyId)
            ->whereIn('status', ['registered','partial'])
            ->groupBy('supplier_id')
            ->selectRaw('supplier_id, SUM(total_amount - COALESCE(paid_amount,0)) as unpaid')
            ->get()
            ->keyBy('supplier_id');

        if ($request->input('export') === 'excel') {
            return $this->exportExcel($rows, 'supplier-performance', 'عملکرد تامین‌کنندگان');
        }

        return view('warehouse.reports.supplier-performance', compact(
            'rows','unpaidBySupplier','dateFrom','dateTo'
        ));
    }

    // ─── ۹. گزارش سود و زیان فروش (FIFO/LIFO/میانگین) ──────────────────────
    public function profitLoss(Request $request)
    {
        Gate::authorize('access', 'reports.profit-loss');

        [$tenantId, $companyId] = $this->tenantCtx();

        $method   = $request->input('method', 'avg'); // avg | fifo | lifo
        $dateFrom = $request->date_from ?? now()->startOfMonth()->format('Y-m-d');
        $dateTo   = $request->date_to   ?? now()->format('Y-m-d');
        $warehouses = Warehouse::where('tenant_id', $tenantId)->where('is_active', true)->get();
        $categories = DB::table('categories')->where('tenant_id', $tenantId)->orderBy('title')->get();

        // فروش‌های بازه
        $salesQuery = DB::table('stock_transactions as st')
            ->join('products as p', 'p.id', '=', 'st.product_id')
            ->leftJoin('categories as cat', 'cat.id', '=', 'p.category_id')
            ->leftJoin('measurement_units as mu', 'mu.id', '=', 'st.measurement_unit_id')
            ->where('st.tenant_id', $tenantId)->where('st.company_id', $companyId)
            ->where('st.status', 'approved')
            ->where('st.type', 'sale')
            ->whereDate('st.created_at', '>=', $dateFrom)
            ->whereDate('st.created_at', '<=', $dateTo)
            ->groupBy('p.id', 'p.title', 'p.sku', 'cat.title', 'mu.title')
            ->select([
                'p.id as product_id', 'p.title as product_title', 'p.sku',
                'cat.title as category', 'mu.title as unit',
                DB::raw('SUM(st.quantity) as sold_qty'),
                DB::raw('SUM(st.quantity * COALESCE(st.unit_price,0)) as revenue'),
            ]);

        if ($request->filled('warehouse_id')) $salesQuery->where('st.warehouse_id', $request->warehouse_id);
        if ($request->filled('category_id'))  $salesQuery->where('p.category_id', $request->category_id);

        $salesRows = $salesQuery->get();

        // محاسبه بهای تمام‌شده بر اساس روش انتخابی
        $rows = $salesRows->map(function ($row) use ($tenantId, $companyId, $method, $dateFrom, $dateTo, $request) {
            $cost = $this->calcCost($tenantId, $companyId, $row->product_id, $row->sold_qty, $method, $dateFrom, $dateTo, $request->warehouse_id ?? null);
            $row->cost           = $cost;
            $row->gross_profit   = $row->revenue - $cost;
            $row->profit_margin  = $row->revenue > 0 ? round(($row->gross_profit / $row->revenue) * 100, 1) : 0;
            return $row;
        })->sortByDesc('gross_profit')->values();

        $totals = [
            'revenue'      => $rows->sum('revenue'),
            'cost'         => $rows->sum('cost'),
            'gross_profit' => $rows->sum('gross_profit'),
            'margin'       => $rows->sum('revenue') > 0
                ? round(($rows->sum('gross_profit') / $rows->sum('revenue')) * 100, 1)
                : 0,
        ];

        if ($request->input('export') === 'excel') {
            return $this->exportExcel($rows, 'profit-loss', "سود و زیان فروش ({$method})");
        }

        return view('warehouse.reports.profit-loss', compact(
            'rows', 'totals', 'warehouses', 'categories', 'dateFrom', 'dateTo', 'method'
        ));
    }

    // ─── ۱۰. ارزیابی موجودی (FIFO / LIFO / میانگین موزون) ──────────────────
    public function inventoryValuation(Request $request)
    {
        Gate::authorize('access', 'reports.inventory-valuation');

        [$tenantId, $companyId] = $this->tenantCtx();

        $method     = $request->input('method', 'avg');
        $warehouses = Warehouse::where('tenant_id', $tenantId)->where('is_active', true)->get();
        $categories = DB::table('categories')->where('tenant_id', $tenantId)->orderBy('title')->get();

        $inTypes  = ['purchase','return_sale','opening','transfer_in','adjustment_in','asset_return'];
        $outTypes = ['sale','return_purchase','transfer_out','adjustment_out','scrap','asset_assign'];

        // موجودی فعلی هر کالا
        $stockQuery = DB::table('stock_transactions as st')
            ->join('products as p', 'p.id', '=', 'st.product_id')
            ->leftJoin('categories as cat', 'cat.id', '=', 'p.category_id')
            ->leftJoin('measurement_units as mu', 'mu.id', '=', 'st.measurement_unit_id')
            ->where('st.tenant_id', $tenantId)->where('st.company_id', $companyId)
            ->where('st.status', 'approved')
            ->groupBy('p.id', 'p.title', 'p.sku', 'cat.title', 'mu.title')
            ->select([
                'p.id as product_id', 'p.title as product_title', 'p.sku',
                'cat.title as category', 'mu.title as unit',
                DB::raw("SUM(CASE WHEN st.type IN ('".implode("','", $inTypes)."') THEN st.quantity ELSE 0 END)
                       - SUM(CASE WHEN st.type IN ('".implode("','", $outTypes)."') THEN st.quantity ELSE 0 END) AS current_stock"),
            ]);

        if ($request->filled('warehouse_id')) $stockQuery->where('st.warehouse_id', $request->warehouse_id);
        if ($request->filled('category_id'))  $stockQuery->where('p.category_id', $request->category_id);

        $stockRows = $stockQuery->havingRaw('current_stock > 0')->get();

        // محاسبه ارزش بر اساس روش
        $rows = $stockRows->map(function ($row) use ($tenantId, $companyId, $method, $request) {
            $unitCost = $this->calcUnitCost($tenantId, $companyId, $row->product_id, $method, $request->warehouse_id ?? null);
            $row->unit_cost    = $unitCost;
            $row->total_value  = round($row->current_stock * $unitCost, 2);
            return $row;
        })->sortByDesc('total_value')->values();

        $totalValue = $rows->sum('total_value');

        if ($request->input('export') === 'excel') {
            return $this->exportExcel($rows, 'inventory-valuation', "ارزیابی موجودی ({$method})");
        }

        return view('warehouse.reports.inventory-valuation', compact(
            'rows', 'totalValue', 'warehouses', 'categories', 'method'
        ));
    }

    // ─── ۱۱. کارت موجودی (Stock Card) پیشرفته ───────────────────────────────
    public function stockCard(Request $request)
    {
        Gate::authorize('access', 'reports.stock-card');

        [$tenantId, $companyId] = $this->tenantCtx();

        $products   = Product::where('tenant_id', $tenantId)->where('is_active', true)->orderBy('title')->get();
        $warehouses = Warehouse::where('tenant_id', $tenantId)->where('is_active', true)->get();

        $rows       = collect();
        $product    = null;
        $openingBal = 0;
        $closingBal = 0;
        $summary    = [];

        if ($request->filled('product_id')) {
            $product = Product::where('tenant_id', $tenantId)->findOrFail($request->product_id);

            $dateFrom = $request->date_from ?? now()->startOfMonth()->format('Y-m-d');
            $dateTo   = $request->date_to   ?? now()->format('Y-m-d');

            // موجودی افتتاحی پیش از بازه
            $openingBal = $this->calcBalance($tenantId, $companyId, $request->product_id,
                $request->warehouse_id ?? null, null, $dateFrom, exclusive: true);

            $txQuery = DB::table('stock_transactions as st')
                ->join('warehouses as wh', 'wh.id', '=', 'st.warehouse_id')
                ->leftJoin('users as u', 'u.id', '=', 'st.created_by')
                ->where('st.tenant_id',  $tenantId)
                ->where('st.company_id', $companyId)
                ->where('st.status',     'approved')
                ->where('st.product_id', $request->product_id)
                ->whereDate('st.transaction_date', '>=', $dateFrom)
                ->whereDate('st.transaction_date', '<=', $dateTo)
                ->select([
                    'st.id', 'st.type', 'st.quantity', 'st.unit_price', 'st.description',
                    'st.transaction_date', 'st.reference_type', 'st.reference_id',
                    'wh.title as warehouse_title', 'u.name as user_name',
                    DB::raw("CASE WHEN st.type IN ('purchase','return_sale','opening','transfer_in','adjustment_in','asset_return') THEN st.quantity ELSE 0 END as qty_in"),
                    DB::raw("CASE WHEN st.type IN ('sale','return_purchase','transfer_out','adjustment_out','scrap','asset_assign') THEN st.quantity ELSE 0 END as qty_out"),
                ])
                ->orderBy('st.transaction_date')->orderBy('st.id');

            if ($request->filled('warehouse_id')) $txQuery->where('st.warehouse_id', $request->warehouse_id);

            $rawRows = $txQuery->get();

            $running = $openingBal;
            $rows = $rawRows->map(function ($r) use (&$running) {
                $running       += $r->qty_in - $r->qty_out;
                $r->balance     = $running;
                $r->line_value  = round($r->quantity * ($r->unit_price ?? 0), 2);
                return $r;
            });

            $closingBal = $running;

            $summary = [
                'total_in'    => $rows->sum('qty_in'),
                'total_out'   => $rows->sum('qty_out'),
                'value_in'    => $rows->where('qty_in', '>', 0)->sum('line_value'),
                'value_out'   => $rows->where('qty_out', '>', 0)->sum('line_value'),
                'tx_count'    => $rows->count(),
            ];

            if ($request->input('export') === 'excel') {
                return $this->exportExcel($rows, 'stock-card', 'کارت موجودی — ' . $product->title);
            }
        }

        return view('warehouse.reports.stock-card', compact(
            'products', 'warehouses', 'rows', 'product', 'openingBal', 'closingBal', 'summary'
        ));
    }

    // ─── helpers: محاسبه بهای تمام‌شده ──────────────────────────────────────
    private function calcCost(int $tenantId, int $companyId, int $productId, float $soldQty, string $method, string $dateFrom, string $dateTo, ?int $warehouseId): float
    {
        // خریدهای تا پایان بازه
        $purchases = DB::table('stock_transactions')
            ->where('tenant_id', $tenantId)->where('company_id', $companyId)
            ->where('product_id', $productId)->where('status', 'approved')
            ->whereIn('type', ['purchase', 'opening'])
            ->whereDate('created_at', '<=', $dateTo)
            ->when($warehouseId, fn($q) => $q->where('warehouse_id', $warehouseId))
            ->orderBy('created_at')
            ->select('quantity', 'unit_price', 'created_at')
            ->get();

        if ($purchases->isEmpty()) return 0;

        return match($method) {
            'avg'  => $this->avgCost($purchases, $soldQty),
            'fifo' => $this->fifoCost($purchases, $soldQty),
            'lifo' => $this->lifoCost($purchases, $soldQty),
            default => $this->avgCost($purchases, $soldQty),
        };
    }

    private function calcUnitCost(int $tenantId, int $companyId, int $productId, string $method, ?int $warehouseId): float
    {
        $purchases = DB::table('stock_transactions')
            ->where('tenant_id', $tenantId)->where('company_id', $companyId)
            ->where('product_id', $productId)->where('status', 'approved')
            ->whereIn('type', ['purchase', 'opening'])
            ->when($warehouseId, fn($q) => $q->where('warehouse_id', $warehouseId))
            ->orderBy('created_at')
            ->select('quantity', 'unit_price')
            ->get();

        if ($purchases->isEmpty()) return 0;
        $totalQty = $purchases->sum('quantity');
        if ($totalQty <= 0) return 0;

        return match($method) {
            'fifo' => (float) $purchases->first()->unit_price,
            'lifo' => (float) $purchases->last()->unit_price,
            default => round($purchases->sum(fn($p) => $p->quantity * $p->unit_price) / $totalQty, 4),
        };
    }

    private function avgCost($purchases, float $qty): float
    {
        $totalQty = $purchases->sum('quantity');
        if ($totalQty <= 0) return 0;
        $weightedAvg = $purchases->sum(fn($p) => $p->quantity * $p->unit_price) / $totalQty;
        return round($qty * $weightedAvg, 2);
    }

    private function fifoCost($purchases, float $qty): float
    {
        $remaining = $qty;
        $cost      = 0;
        foreach ($purchases as $p) {
            if ($remaining <= 0) break;
            $use       = min($remaining, $p->quantity);
            $cost     += $use * $p->unit_price;
            $remaining -= $use;
        }
        return round($cost, 2);
    }

    private function lifoCost($purchases, float $qty): float
    {
        $remaining = $qty;
        $cost      = 0;
        foreach ($purchases->reverse() as $p) {
            if ($remaining <= 0) break;
            $use       = min($remaining, $p->quantity);
            $cost     += $use * $p->unit_price;
            $remaining -= $use;
        }
        return round($cost, 2);
    }

    // ─── ۸. خروجی Excel (CSV) ────────────────────────────────────────────────
    // NOTE: new report methods (profitLoss, inventoryValuation, stockCard) added above
    private function exportExcel($rows, string $filename, string $title): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $date = now()->format('Y-m-d');
        return response()->streamDownload(function () use ($rows, $title) {
            $out = fopen('php://output', 'w');
            // BOM برای پشتیبانی از فارسی در Excel
            fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));
            // عنوان
            fputcsv($out, [$title, '', '', '', '', '', '', '']);
            fputcsv($out, []);

            if ($rows->isEmpty()) {
                fputcsv($out, ['داده‌ای یافت نشد']);
                fclose($out);
                return;
            }

            // سرستون از کلیدهای آبجکت اول
            fputcsv($out, array_keys((array)$rows->first()));
            foreach ($rows as $row) {
                fputcsv($out, array_values((array)$row));
            }
            fclose($out);
        }, "{$filename}-{$date}.csv", [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}-{$date}.csv\"",
        ]);
    }

    // ─── helpers ──────────────────────────────────────────────────────────────
    private function tenantCtx(): array
    {
        return [$this->manager->getTenantId(), $this->manager->getCompanyId()];
    }

    private function calcBalance(int $tenantId, int $companyId, int $productId, ?int $warehouseId, ?string $dateTo, ?string $dateFrom, bool $exclusive = false): float
    {
        $q = DB::table('stock_transactions')
            ->where('tenant_id',  $tenantId)
            ->where('company_id', $companyId)
            ->where('product_id', $productId)
            ->where('status',     'approved');

        if ($warehouseId) $q->where('warehouse_id', $warehouseId);
        if ($dateTo)      $q->whereDate('created_at', '<=', $dateTo);
        if ($dateFrom && $exclusive) $q->whereDate('created_at', '<', $dateFrom);

        return (float) $q->sum(DB::raw(
            "CASE WHEN type IN ('purchase','return_sale','opening','transfer_in','adjustment_in','asset_return') THEN quantity
                  WHEN type IN ('sale','return_purchase','transfer_out','adjustment_out','scrap','asset_assign') THEN -quantity
                  ELSE 0 END"
        ));
    }
}
