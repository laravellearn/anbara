<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\ItemRequest;
use App\Models\Product;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseOrder;
use App\Models\PurchaseRequest;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\WarehouseDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\TenantManager;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        /** @var TenantManager $manager */
        $manager = app(TenantManager::class);
        $companyId = $manager->getCompanyId();

        if (auth()->user()?->isSuperAdmin()) {
            return redirect()->route('super-admin.dashboard');
        }

        $tenant = $manager->getTenant();

        $activeSubscription = $tenant ? $tenant->subscriptions()->with('plan')->where('status', 'active')->where('starts_at', '<=', now())->where(function ($q) {
            $q->whereNull('ends_at')->orWhere('ends_at', '>=', now());
        })->first() : null;

        $tenantId = $tenant->id;

        // ─── آمار پایه ───────────────────────────────────────────────────────
        $productsCount   = Product::where('tenant_id', $tenantId)->count();
        $warehousesCount = Warehouse::where('tenant_id', $tenantId)->count();
        $usersCount      = User::where('tenant_id', $tenantId)->count();
        $categoriesCount = Category::where('tenant_id', $tenantId)->count();

        // ─── هشدارهای موجودی ──────────────────────────────────────────────────
        $belowMinStock = (int) DB::table('stock_transactions as st')
            ->join('products as p', 'p.id', '=', 'st.product_id')
            ->where('st.tenant_id', $tenantId)
            ->where('st.status', 'approved')
            ->where('p.minimum_stock', '>', 0)
            ->groupBy('p.id', 'st.warehouse_id')
            ->havingRaw(
                'SUM(CASE WHEN st.type IN ("purchase","return_sale","opening","transfer_in","adjustment_in","asset_return") THEN st.quantity ELSE 0 END)
               - SUM(CASE WHEN st.type IN ("sale","return_purchase","transfer_out","adjustment_out","scrap","asset_assign") THEN st.quantity ELSE 0 END) < p.minimum_stock'
            )
            ->get()->count();

        // ─── مانیتورینگ چرخه خرید ────────────────────────────────────────────
        $pendingPR = PurchaseRequest::where('tenant_id', $tenantId)
            ->where('status', 'submitted')->count();

        $pendingIR = ItemRequest::where('tenant_id', $tenantId)
            ->where('status', 'submitted')->count();

        $openPO = PurchaseOrder::where('tenant_id', $tenantId)
            ->whereIn('status', ['confirmed', 'sent', 'partial_received'])->count();

        $unpaidInvoices = PurchaseInvoice::where('tenant_id', $tenantId)
            ->whereIn('status', ['registered', 'partial'])->count();
        $unpaidAmount   = PurchaseInvoice::where('tenant_id', $tenantId)
            ->whereIn('status', ['registered', 'partial'])
            ->sum(DB::raw('total_amount - COALESCE(paid_amount,0)'));

        // ─── اسناد انبار در انتظار ────────────────────────────────────────────
        $pendingDocs = WarehouseDocument::where('tenant_id', $tenantId)
            ->where('status', 'pending')->count();

        // ─── فعالیت‌های اخیر ──────────────────────────────────────────────────
        $recentActivities = ActivityLog::where('tenant_id', $tenantId)
            ->latest()->take(10)->get();

        // ─── اسناد انبار اخیر ────────────────────────────────────────────────
        $recentDocuments = WarehouseDocument::where('tenant_id', $tenantId)
            ->with('createdBy')
            ->latest()->take(5)->get();

        $data = [
            'productsCount'    => $productsCount,
            'warehousesCount'  => $warehousesCount,
            'usersCount'       => $usersCount,
            'categoriesCount'  => $categoriesCount,
            'belowMinStock'    => $belowMinStock,
            'pendingPR'        => $pendingPR,
            'pendingIR'        => $pendingIR,
            'openPO'           => $openPO,
            'unpaidInvoices'   => $unpaidInvoices,
            'unpaidAmount'     => $unpaidAmount,
            'pendingDocs'      => $pendingDocs,
            'activeSubscription' => $activeSubscription,
            'recentActivities' => $recentActivities,
            'recentDocuments'  => $recentDocuments,
            'user'             => $user,
            'companyId'        => $companyId,
        ];

        return view('dashboard', $data);
    }

    // ─── JSON endpoint برای polling داشبورد ──────────────────────────────────
    public function stats()
    {
        $manager   = app(TenantManager::class);
        $tenant    = $manager->getTenant();
        if (! $tenant) return response()->json([]);

        $tenantId  = $tenant->id;
        $companyId = $manager->getCompanyId();

        $belowMinStock = (int) DB::table('stock_transactions as st')
            ->join('products as p', 'p.id', '=', 'st.product_id')
            ->where('st.tenant_id', $tenantId)
            ->where('st.status', 'approved')
            ->where('p.minimum_stock', '>', 0)
            ->groupBy('p.id', 'st.warehouse_id')
            ->havingRaw(
                'SUM(CASE WHEN st.type IN ("purchase","return_sale","opening","transfer_in","adjustment_in","asset_return") THEN st.quantity ELSE 0 END)
               - SUM(CASE WHEN st.type IN ("sale","return_purchase","transfer_out","adjustment_out","scrap","asset_assign") THEN st.quantity ELSE 0 END) < p.minimum_stock'
            )
            ->get()->count();

        return response()->json([
            'below_min_stock' => $belowMinStock,
            'pending_pr'      => \App\Models\PurchaseRequest::where('tenant_id', $tenantId)->where('status', 'submitted')->count(),
            'pending_ir'      => \App\Models\ItemRequest::where('tenant_id', $tenantId)->where('status', 'submitted')->count(),
            'open_po'         => \App\Models\PurchaseOrder::where('tenant_id', $tenantId)->whereIn('status', ['confirmed', 'sent', 'partial_received'])->count(),
            'unpaid_invoices' => \App\Models\PurchaseInvoice::where('tenant_id', $tenantId)->whereIn('status', ['registered', 'partial'])->count(),
            'pending_docs'    => \App\Models\WarehouseDocument::where('tenant_id', $tenantId)->where('status', 'pending')->count(),
            'updated_at'      => now()->format('H:i:s'),
        ]);
    }
}
