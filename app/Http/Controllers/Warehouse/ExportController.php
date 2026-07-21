<?php

namespace App\Http\Controllers\Warehouse;

use App\Models\Product;
use App\Models\StockTransaction;
use App\Models\SalesInvoice;
use App\Models\PurchaseOrder;
use App\Models\Contact;
use App\Services\StockService;
use App\Services\TenantManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;

class ExportController extends BaseController
{
    public function __construct(
        TenantManager $manager,
        private StockService $stockService
    ) {
        parent::__construct($manager);
    }

    // ─── موجودی انبار — CSV ───────────────────────────────────────────────────
    public function inventoryCsv(Request $request)
    {
        Gate::authorize('access', 'reports.inventory');
        [$tenantId, $companyId] = $this->ctx();

        $warehouseId = $request->filled('warehouse_id') ? (int)$request->warehouse_id : null;
        $rows        = $this->stockService->getStockList($tenantId, $companyId, $warehouseId);

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="inventory_' . now()->format('Ymd_His') . '.csv"',
        ];

        $callback = function () use ($rows) {
            $fh = fopen('php://output', 'w');
            // BOM for Excel UTF-8
            fwrite($fh, "\xEF\xBB\xBF");
            fputcsv($fh, ['کد کالا', 'نام کالا', 'انبار', 'موجودی', 'واحد', 'حداقل موجودی']);
            foreach ($rows as $row) {
                fputcsv($fh, [
                    $row->sku ?? '',
                    $row->product_title,
                    $row->warehouse_title ?? '',
                    $row->quantity,
                    $row->unit_title ?? '',
                    $row->minimum_stock ?? 0,
                ]);
            }
            fclose($fh);
        };

        return response()->stream($callback, 200, $headers);
    }

    // ─── لیست کالاها — CSV ───────────────────────────────────────────────────
    public function productsCsv()
    {
        Gate::authorize('access', 'products.view');
        [$tenantId] = $this->ctx();

        $products = Product::where('tenant_id', $tenantId)
            ->with(['category', 'measurementUnit'])
            ->get();

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="products_' . now()->format('Ymd_His') . '.csv"',
        ];

        $callback = function () use ($products) {
            $fh = fopen('php://output', 'w');
            fwrite($fh, "\xEF\xBB\xBF");
            fputcsv($fh, ['کد', 'نام کالا', 'دسته‌بندی', 'واحد', 'قیمت خرید', 'قیمت فروش', 'حداقل موجودی', 'وضعیت']);
            foreach ($products as $p) {
                fputcsv($fh, [
                    $p->sku ?? '',
                    $p->title,
                    $p->category?->title ?? '',
                    $p->measurementUnit?->title ?? '',
                    $p->purchase_price ?? 0,
                    $p->sale_price ?? 0,
                    $p->minimum_stock ?? 0,
                    $p->is_active ? 'فعال' : 'غیرفعال',
                ]);
            }
            fclose($fh);
        };

        return response()->stream($callback, 200, $headers);
    }

    // ─── فاکتورهای فروش — CSV ────────────────────────────────────────────────
    public function salesInvoicesCsv(Request $request)
    {
        Gate::authorize('access', 'sales-invoices.view');
        [$tenantId, $companyId] = $this->ctx();

        $query = SalesInvoice::with('customer')
            ->forTenant($tenantId, $companyId);

        if ($request->filled('date_from')) { $query->whereDate('invoice_date', '>=', $request->date_from); }
        if ($request->filled('date_to'))   { $query->whereDate('invoice_date', '<=', $request->date_to); }
        if ($request->filled('status'))    { $query->where('status', $request->status); }

        $invoices = $query->get();

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="sales_invoices_' . now()->format('Ymd_His') . '.csv"',
        ];

        $statusLabels = SalesInvoice::statusLabels();

        $callback = function () use ($invoices, $statusLabels) {
            $fh = fopen('php://output', 'w');
            fwrite($fh, "\xEF\xBB\xBF");
            fputcsv($fh, ['شماره فاکتور', 'تاریخ', 'مشتری', 'جمع کل', 'مبلغ پرداختی', 'مانده', 'وضعیت']);
            foreach ($invoices as $inv) {
                fputcsv($fh, [
                    $inv->invoice_number,
                    $inv->invoice_date?->format('Y-m-d'),
                    $inv->customer?->name ?? '',
                    number_format($inv->total_amount),
                    number_format($inv->paid_amount),
                    number_format($inv->remainingAmount()),
                    $statusLabels[$inv->status] ?? $inv->status,
                ]);
            }
            fclose($fh);
        };

        return response()->stream($callback, 200, $headers);
    }

    // ─── سفارشات خرید — CSV ──────────────────────────────────────────────────
    public function purchaseOrdersCsv(Request $request)
    {
        Gate::authorize('access', 'purchase-orders.view');
        [$tenantId, $companyId] = $this->ctx();

        $orders = PurchaseOrder::with('supplier')
            ->forTenant($tenantId, $companyId)
            ->get();

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="purchase_orders_' . now()->format('Ymd_His') . '.csv"',
        ];

        $callback = function () use ($orders) {
            $fh = fopen('php://output', 'w');
            fwrite($fh, "\xEF\xBB\xBF");
            fputcsv($fh, ['شماره PO', 'تاریخ', 'تأمین‌کننده', 'جمع کل', 'وضعیت']);
            foreach ($orders as $po) {
                fputcsv($fh, [
                    $po->po_number,
                    $po->order_date?->format('Y-m-d'),
                    $po->supplier?->name ?? '',
                    number_format($po->total_amount ?? 0),
                    $po->status,
                ]);
            }
            fclose($fh);
        };

        return response()->stream($callback, 200, $headers);
    }

    // ─── helper ───────────────────────────────────────────────────────────────
    private function ctx(): array
    {
        return [$this->manager->getTenantId(), $this->manager->getCompanyId()];
    }
}
