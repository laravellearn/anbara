<?php

namespace App\Http\Controllers\Warehouse;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\PurchaseOrder;
use App\Models\WarehouseDocument;
use App\Models\Warehouse;

class DashboardController extends BaseController
{
    public function index()
    {
        $tenantId  = $this->manager->getTenantId();
        $companyId = $this->manager->getCompanyId();

        // ─── KPI cards ──────────────────────────────────────────────────────
        $kpi = $this->buildKpi($tenantId, $companyId);

        // ─── نمودار ورود/خروج ۶ ماه اخیر (ماه به ماه) ──────────────────────
        $monthlyChart = $this->monthlyInOut($tenantId, $companyId);

        // ─── ۵ کالای پرتحرک (بیشترین تراکنش ماه جاری) ─────────────────────
        $topProducts = $this->topMovingProducts($tenantId, $companyId);

        // ─── آخرین اسناد انبار (۸ تا) ───────────────────────────────────────
        $recentDocs = WarehouseDocument::with(['warehouse'])
            ->where('tenant_id', $tenantId)->where('company_id', $companyId)
            ->latest()->limit(8)->get();

        // ─── آخرین سفارشات خرید (۵ تا) ─────────────────────────────────────
        $recentPos = PurchaseOrder::with(['supplier'])
            ->where('tenant_id', $tenantId)->where('company_id', $companyId)
            ->latest('order_date')->limit(5)->get();

        // ─── کالاهای زیر حداقل (تا ۵ مورد) ────────────────────────────────
        $belowMin = $this->belowMinimumItems($tenantId, $companyId, 5);

        // ─── نمودار ABC انبار ────────────────────────────────────────────────
        $abcChart = $this->abcAnalysis($tenantId, $companyId);

        // ─── محصولات پرفروش (فروش ماه جاری) ────────────────────────────────
        $topSelling = $this->topSellingProducts($tenantId, $companyId);

        // ─── روند موجودی ماهانه (۶ ماه) ────────────────────────────────────
        $stockTrend = $this->monthlyStockTrend($tenantId, $companyId);

        // ─── آمار فاکتورها و پرداخت‌ها ──────────────────────────────────────
        $invoiceKpi = $this->invoiceKpi($tenantId, $companyId);

        return view('warehouse.dashboard', compact(
            'kpi', 'monthlyChart', 'topProducts', 'recentDocs', 'recentPos', 'belowMin',
            'abcChart', 'topSelling', 'stockTrend', 'invoiceKpi'
        ));
    }

    // ─── helpers ──────────────────────────────────────────────────────────────
    private function buildKpi(int $tenantId, int $companyId): array
    {
        $base = fn($table) => DB::table($table)
            ->where('tenant_id', $tenantId)->where('company_id', $companyId);

        $stockIn  = DB::table('stock_transactions')
            ->where('tenant_id', $tenantId)->where('company_id', $companyId)
            ->where('status', 'approved')
            ->whereIn('type', ['purchase_receipt','return_from_customer','opening','transfer_in','adjustment_in','receipt','return_in'])
            ->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)
            ->sum('quantity');

        $stockOut = DB::table('stock_transactions')
            ->where('tenant_id', $tenantId)->where('company_id', $companyId)
            ->where('status', 'approved')
            ->whereIn('type', ['issue','return_to_supplier','transfer_out','adjustment_out','return_out'])
            ->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)
            ->sum('quantity');

        return [
            'total_products'    => DB::table('products')->where('tenant_id', $tenantId)->where('is_active', true)->count(),
            'total_warehouses'  => Warehouse::where('tenant_id', $tenantId)->where('is_active', true)->count(),
            'pending_docs'      => WarehouseDocument::where('tenant_id', $tenantId)->where('company_id', $companyId)->where('status', 'pending')->count(),
            'open_po'           => PurchaseOrder::where('tenant_id', $tenantId)->where('company_id', $companyId)->whereIn('status', ['confirmed','sent','partial_received'])->count(),
            'below_min_count'   => $this->belowMinimumCount($tenantId, $companyId),
            'month_stock_in'    => (float)$stockIn,
            'month_stock_out'   => (float)$stockOut,
        ];
    }

    private function monthlyInOut(int $tenantId, int $companyId): array
    {
        $months = collect();
        for ($i = 5; $i >= 0; $i--) {
            $months->push(now()->subMonths($i)->format('Y-m'));
        }

        $inTypes  = ['purchase_receipt','return_from_customer','opening','transfer_in','adjustment_in','receipt','return_in'];
        $outTypes = ['issue','return_to_supplier','transfer_out','adjustment_out','return_out'];

        $rows = DB::table('stock_transactions')
            ->where('tenant_id', $tenantId)->where('company_id', $companyId)
            ->where('status', 'approved')
            ->whereDate('created_at', '>=', now()->subMonths(5)->startOfMonth())
            ->select([
                DB::raw("DATE_FORMAT(created_at,'%Y-%m') as month"),
                'type',
                DB::raw('SUM(quantity) as qty'),
            ])
            ->groupBy('month', 'type')
            ->get()
            ->groupBy('month');

        $labels = [];
        $inData = [];
        $outData = [];

        foreach ($months as $m) {
            $labels[]  = $m;
            $monthRows = $rows->get($m, collect());
            $in  = $monthRows->whereIn('type', $inTypes)->sum('qty');
            $out = $monthRows->whereIn('type', $outTypes)->sum('qty');
            $inData[]  = round((float)$in, 2);
            $outData[] = round((float)$out, 2);
        }

        return compact('labels', 'inData', 'outData');
    }

    private function topMovingProducts(int $tenantId, int $companyId): \Illuminate\Support\Collection
    {
        return DB::table('stock_transactions as st')
            ->join('products as p', 'p.id', '=', 'st.product_id')
            ->where('st.tenant_id', $tenantId)->where('st.company_id', $companyId)
            ->where('st.status', 'approved')
            ->whereMonth('st.created_at', now()->month)->whereYear('st.created_at', now()->year)
            ->groupBy('p.id', 'p.title')
            ->select('p.id', 'p.title', DB::raw('COUNT(*) as tx_count'), DB::raw('SUM(st.quantity) as total_qty'))
            ->orderByDesc('tx_count')
            ->limit(5)
            ->get();
    }

    private function belowMinimumItems(int $tenantId, int $companyId, int $limit): \Illuminate\Support\Collection
    {
        return DB::table('stock_transactions as st')
            ->join('products as p', 'p.id', '=', 'st.product_id')
            ->join('warehouses as wh', 'wh.id', '=', 'st.warehouse_id')
            ->where('st.tenant_id', $tenantId)->where('st.company_id', $companyId)
            ->where('st.status', 'approved')->where('p.minimum_stock', '>', 0)
            ->groupBy('p.id','p.title','p.minimum_stock','wh.id','wh.title')
            ->select([
                'p.id','p.title as product','p.minimum_stock','wh.title as warehouse',
                DB::raw("SUM(CASE WHEN st.type IN ('purchase_receipt','return_from_customer','opening','transfer_in','adjustment_in','receipt','return_in') THEN st.quantity ELSE 0 END)
                       - SUM(CASE WHEN st.type IN ('issue','return_to_supplier','transfer_out','adjustment_out','return_out') THEN st.quantity ELSE 0 END) AS current_stock"),
            ])
            ->havingRaw('current_stock < p.minimum_stock')
            ->orderByRaw('(p.minimum_stock - current_stock) DESC')
            ->limit($limit)->get();
    }

    private function belowMinimumCount(int $tenantId, int $companyId): int
    {
        return $this->belowMinimumItems($tenantId, $companyId, 9999)->count();
    }
}

