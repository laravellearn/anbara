<?php

namespace App\Services;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\WarehouseDocument;
use Illuminate\Support\Facades\DB;

class PurchaseOrderService
{
    public function __construct(
        private WarehouseDocumentService $docService
    ) {}

    /** تولید شماره PO یکتا */
    public function generatePoNumber(int $tenantId): string
    {
        $year = now()->format('Y');
        $last = PurchaseOrder::where('tenant_id', $tenantId)
            ->whereYear('created_at', $year)
            ->lockForUpdate()
            ->count();
        $seq = str_pad($last + 1, 5, '0', STR_PAD_LEFT);
        return "PO-{$year}-{$seq}";
    }

    /** تأیید سفارش */
    public function confirm(PurchaseOrder $po, int $userId): void
    {
        if (!$po->canConfirm()) {
            throw new \RuntimeException('سفارش در وضعیت مجاز برای تأیید نیست.');
        }
        $po->update([
            'status'       => PurchaseOrder::STATUS_CONFIRMED,
            'confirmed_by' => $userId,
            'confirmed_at' => now(),
        ]);
    }

    /** ارسال به تأمین‌کننده */
    public function markSent(PurchaseOrder $po): void
    {
        if (!$po->canSend()) {
            throw new \RuntimeException('سفارش باید ابتدا تأیید شود.');
        }
        $po->update([
            'status'  => PurchaseOrder::STATUS_SENT,
            'sent_at' => now(),
        ]);
    }

    /**
     * دریافت کالا — ایجاد سند رسید انبار به صورت خودکار
     * $received = [ ['item_id' => X, 'quantity' => Y], ... ]
     */
    public function receive(PurchaseOrder $po, array $received, int $userId): WarehouseDocument
    {
        if (!$po->canReceive()) {
            throw new \RuntimeException('سفارش در وضعیت مجاز برای دریافت نیست.');
        }

        // تمام عملیات (ساخت سند + تأیید + به‌روزرسانی PO) در یک تراکنش
        return DB::transaction(function () use ($po, $received, $userId) {

            // ساخت سند رسید انبار
            $doc = WarehouseDocument::create([
                'tenant_id'        => $po->tenant_id,
                'company_id'       => $po->company_id,
                'document_number'  => $this->docService->generateDocumentNumber('receipt', $po->tenant_id),
                'type'             => WarehouseDocument::TYPE_RECEIPT,
                'status'           => WarehouseDocument::STATUS_PENDING,
                'warehouse_id'     => $po->warehouse_id,
                'contact_id'       => $po->supplier_id,
                'fiscal_year_id'   => $po->fiscal_year_id,
                'cost_center_id'   => $po->cost_center_id,
                'document_date'    => now()->toDateString(),
                'reference_number' => $po->po_number,
                'description'      => "دریافت بر اساس سفارش {$po->po_number}",
                'created_by'       => $userId,
            ]);

            $allReceived = true;

            foreach ($received as $i => $entry) {
                $item = PurchaseOrderItem::findOrFail($entry['item_id']);
                if ($item->purchase_order_id !== $po->id) continue;

                $qty = min((float)$entry['quantity'], $item->remaining_qty);
                if ($qty <= 0) continue;

                $doc->items()->create([
                    'product_id'          => $item->product_id,
                    'measurement_unit_id' => $item->measurement_unit_id,
                    'quantity'            => $qty,
                    'unit_price'          => $item->unit_price,
                    'sort_order'          => $i,
                ]);

                $item->increment('quantity_received', $qty);
                $item->update(['warehouse_document_id' => $doc->id]);

                if (!$item->fresh()->isFullyReceived()) {
                    $allReceived = false;
                }
            }

            // تأیید سند رسید انبار (ایجاد stock_transactions) داخل همین تراکنش
            $this->docService->approve($doc, $userId);

            // به‌روزرسانی وضعیت PO پس از تأیید موفق سند
            $po->update([
                'status' => $allReceived
                    ? PurchaseOrder::STATUS_RECEIVED
                    : PurchaseOrder::STATUS_PARTIAL_RECEIVED,
                'actual_delivery_date' => now()->toDateString(),
            ]);

            return $doc;
        });
    }

    /** بستن سفارش */
    public function close(PurchaseOrder $po, int $userId): void
    {
        if (!$po->canClose()) {
            throw new \RuntimeException('سفارش در وضعیت مجاز برای بستن نیست.');
        }
        $po->update([
            'status'    => PurchaseOrder::STATUS_CLOSED,
            'closed_by' => $userId,
            'closed_at' => now(),
        ]);
    }

    /** لغو سفارش */
    public function cancel(PurchaseOrder $po, ?string $reason = null): void
    {
        if (!$po->canCancel()) {
            throw new \RuntimeException('این سفارش قابل لغو نیست.');
        }
        $po->update([
            'status'              => PurchaseOrder::STATUS_CANCELLED,
            'cancellation_reason' => $reason,
        ]);
    }
}
