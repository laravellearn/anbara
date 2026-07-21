<?php

namespace App\Services;

use App\Models\SalesInvoice;
use App\Models\SalesInvoiceItem;
use App\Models\WarehouseDocument;
use Illuminate\Support\Facades\DB;

class SalesInvoiceService
{
    public function __construct(
        private WarehouseDocumentService $docService,
        private NotificationService $notificationService
    ) {}

    /**
     * تولید شماره فاکتور یکتا
     */
    public function generateInvoiceNumber(int $tenantId): string
    {
        return DB::transaction(function () use ($tenantId) {
            $year = now()->format('Y');
            $last = SalesInvoice::where('tenant_id', $tenantId)
                ->whereYear('created_at', $year)
                ->lockForUpdate()
                ->count();
            $seq = str_pad($last + 1, 5, '0', STR_PAD_LEFT);
            return "INV-{$year}-{$seq}";
        });
    }

    /**
     * محاسبه مجموع ردیف‌ها و به‌روزرسانی جمع‌ها در فاکتور
     */
    public function recalculate(SalesInvoice $invoice): void
    {
        $subtotal = $invoice->items()->sum(DB::raw('quantity * unit_price - discount_amount'));
        $discountAmt = $invoice->discount_percent > 0
            ? round($subtotal * $invoice->discount_percent / 100, 4)
            : $invoice->discount_amount;

        $afterDiscount = $subtotal - $discountAmt;
        $taxAmt = round($afterDiscount * $invoice->tax_percent / 100, 4);
        $total  = $afterDiscount + $taxAmt;

        $invoice->update([
            'subtotal'         => round($subtotal, 4),
            'discount_amount'  => round($discountAmt, 4),
            'tax_amount'       => round($taxAmt, 4),
            'total_amount'     => round($total, 4),
        ]);
    }

    /**
     * تأیید فاکتور — اختیاری: صدور حواله خروج از انبار
     */
    public function confirm(SalesInvoice $invoice, int $userId, bool $issueDocument = false): void
    {
        if (!$invoice->canConfirm()) {
            throw new \RuntimeException('فاکتور در وضعیت مجاز برای تأیید نیست.');
        }

        DB::transaction(function () use ($invoice, $userId, $issueDocument) {
            $invoice->update([
                'status'       => SalesInvoice::STATUS_CONFIRMED,
                'confirmed_by' => $userId,
                'confirmed_at' => now(),
            ]);

            // صدور حواله خروج از انبار به صورت خودکار
            if ($issueDocument && $invoice->warehouse_id) {
                $doc = WarehouseDocument::create([
                    'tenant_id'       => $invoice->tenant_id,
                    'company_id'      => $invoice->company_id,
                    'document_number' => $this->docService->generateDocumentNumber('issue', $invoice->tenant_id),
                    'type'            => WarehouseDocument::TYPE_ISSUE,
                    'status'          => WarehouseDocument::STATUS_PENDING,
                    'warehouse_id'    => $invoice->warehouse_id,
                    'contact_id'      => $invoice->customer_id,
                    'fiscal_year_id'  => $invoice->fiscal_year_id,
                    'cost_center_id'  => $invoice->cost_center_id,
                    'document_date'   => $invoice->invoice_date->toDateString(),
                    'reference_number'=> $invoice->invoice_number,
                    'description'     => "حواله فروش — فاکتور {$invoice->invoice_number}",
                    'created_by'      => $userId,
                ]);

                foreach ($invoice->items as $i => $item) {
                    $doc->items()->create([
                        'product_id'          => $item->product_id,
                        'measurement_unit_id' => $item->measurement_unit_id,
                        'quantity'            => $item->quantity,
                        'unit_price'          => $item->unit_price,
                        'sort_order'          => $i,
                    ]);
                }

                $this->docService->approve($doc, $userId);

                $invoice->update(['warehouse_document_id' => $doc->id]);
            }
        });
    }

    /**
     * ثبت پرداخت جزئی یا کامل
     */
    public function registerPayment(SalesInvoice $invoice, float $amount): void
    {
        if ($amount <= 0 || $amount > $invoice->remainingAmount()) {
            throw new \RuntimeException('مبلغ پرداخت نامعتبر است.');
        }

        $newPaid = $invoice->paid_amount + $amount;
        $status  = $newPaid >= $invoice->total_amount
            ? SalesInvoice::STATUS_PAID
            : SalesInvoice::STATUS_PARTIALLY_PAID;

        $invoice->update(['paid_amount' => $newPaid, 'status' => $status]);
    }

    /**
     * لغو فاکتور
     */
    public function cancel(SalesInvoice $invoice): void
    {
        if (!$invoice->canCancel()) {
            throw new \RuntimeException('امکان لغو این فاکتور وجود ندارد.');
        }
        $invoice->update(['status' => SalesInvoice::STATUS_CANCELLED]);
    }
}
