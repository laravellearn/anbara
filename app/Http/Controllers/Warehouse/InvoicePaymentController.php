<?php

namespace App\Http\Controllers\Warehouse;

use App\Models\InvoicePayment;
use App\Models\SalesInvoice;
use App\Models\PurchaseInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class InvoicePaymentController extends BaseController
{
    // ─── ثبت پرداخت برای فاکتور فروش ────────────────────────────────────────
    public function storeForSalesInvoice(Request $request, SalesInvoice $salesInvoice)
    {
        Gate::authorize('access', 'invoice-payments.create');
        [$tenantId, $companyId] = $this->ctx();

        $data = $this->validatePayment($request);
        $data = array_merge($data, [
            'tenant_id'       => $tenantId,
            'company_id'      => $companyId,
            'sales_invoice_id'=> $salesInvoice->id,
            'fiscal_year_id'  => $this->manager->getFiscalYear()?->id,
            'created_by'      => auth()->id(),
        ]);

        DB::transaction(function () use ($data, $salesInvoice) {
            InvoicePayment::create($data);
            $this->updateSalesInvoiceStatus($salesInvoice);
        });

        return redirect()->back()->with('toast', [
            'type' => 'success', 'title' => 'پرداخت ثبت شد',
            'message' => 'پرداخت با موفقیت ثبت و تسویه فاکتور به‌روز شد.',
        ]);
    }

    // ─── ثبت پرداخت برای فاکتور خرید ────────────────────────────────────────
    public function storeForPurchaseInvoice(Request $request, PurchaseInvoice $purchaseInvoice)
    {
        Gate::authorize('access', 'invoice-payments.create');
        [$tenantId, $companyId] = $this->ctx();

        $data = $this->validatePayment($request);
        $data = array_merge($data, [
            'tenant_id'           => $tenantId,
            'company_id'          => $companyId,
            'purchase_invoice_id' => $purchaseInvoice->id,
            'fiscal_year_id'      => $this->manager->getFiscalYear()?->id,
            'created_by'          => auth()->id(),
        ]);

        DB::transaction(function () use ($data, $purchaseInvoice) {
            InvoicePayment::create($data);
            $this->updatePurchaseInvoiceStatus($purchaseInvoice);
        });

        return redirect()->back()->with('toast', [
            'type' => 'success', 'title' => 'پرداخت ثبت شد',
            'message' => 'پرداخت با موفقیت ثبت شد.',
        ]);
    }

    // ─── حذف پرداخت ──────────────────────────────────────────────────────────
    public function destroy(InvoicePayment $invoicePayment)
    {
        Gate::authorize('access', 'invoice-payments.delete');

        DB::transaction(function () use ($invoicePayment) {
            $salesInvoice   = $invoicePayment->salesInvoice;
            $purchaseInvoice = $invoicePayment->purchaseInvoice;
            $invoicePayment->delete();

            if ($salesInvoice)   $this->updateSalesInvoiceStatus($salesInvoice);
            if ($purchaseInvoice) $this->updatePurchaseInvoiceStatus($purchaseInvoice);
        });

        return redirect()->back()->with('toast', [
            'type' => 'warning', 'title' => 'پرداخت حذف شد',
            'message' => 'رکورد پرداخت حذف و وضعیت فاکتور به‌روز شد.',
        ]);
    }

    // ─── لیست پرداخت‌های یک فاکتور فروش (AJAX) ──────────────────────────────
    public function forSalesInvoice(SalesInvoice $salesInvoice)
    {
        Gate::authorize('access', 'invoice-payments.view');
        $payments = $salesInvoice->invoicePayments()->with('creator')->latest('payment_date')->get();
        return response()->json($payments);
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────
    private function validatePayment(Request $request): array
    {
        return $request->validate([
            'payment_date'     => 'required|date',
            'amount'           => 'required|numeric|min:0.01',
            'payment_method'   => 'required|in:cash,cheque,bank_transfer,card,other',
            'reference_number' => 'nullable|string|max:100',
            'cheque_date'      => 'nullable|date',
            'bank_name'        => 'nullable|string|max:100',
            'account_number'   => 'nullable|string|max:50',
            'notes'            => 'nullable|string',
        ]);
    }

    private function updateSalesInvoiceStatus(SalesInvoice $invoice): void
    {
        $totalPaid = InvoicePayment::where('sales_invoice_id', $invoice->id)->sum('amount');
        $invoice->update(['paid_amount' => $totalPaid]);

        if ($totalPaid <= 0) {
            $newStatus = 'confirmed';
        } elseif ($totalPaid >= $invoice->total_amount) {
            $newStatus = 'paid';
        } else {
            $newStatus = 'partially_paid';
        }

        if (! in_array($invoice->status, ['draft', 'cancelled'])) {
            $invoice->update(['status' => $newStatus]);
        }
    }

    private function updatePurchaseInvoiceStatus(PurchaseInvoice $invoice): void
    {
        $totalPaid = InvoicePayment::where('purchase_invoice_id', $invoice->id)->sum('amount');
        $subtotal  = $invoice->items()->sum(DB::raw('quantity * unit_price * (1 - discount_percent/100)'));

        if ($totalPaid >= $subtotal && $subtotal > 0) {
            $invoice->update(['status' => 'paid', 'payment_date' => now()->toDateString()]);
        } elseif ($totalPaid > 0) {
            $invoice->update(['status' => 'registered']);
        }
    }

    private function ctx(): array
    {
        return [$this->manager->getTenantId(), $this->manager->getCompanyId()];
    }
}
