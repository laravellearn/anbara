<?php

namespace App\Services;

use App\Enums\InventoryTransactionStatus;
use App\Enums\InventoryTransactionType;
use App\Models\StockTransaction;
use App\Models\WarehouseDocument;
use App\Models\WarehouseDocumentItem;
use Illuminate\Support\Facades\DB;

class WarehouseDocumentService
{
    /**
     * تولید شماره سند یکتا برای هر نوع و tenant
     */
    public function generateDocumentNumber(string $type, int $tenantId): string
    {
        $prefix = WarehouseDocument::typePrefix($type);
        $year   = now()->format('Y');

        $last = WarehouseDocument::where('tenant_id', $tenantId)
            ->where('type', $type)
            ->whereYear('created_at', $year)
            ->lockForUpdate()
            ->count();

        $seq = str_pad($last + 1, 5, '0', STR_PAD_LEFT);
        return "{$prefix}-{$year}-{$seq}";
    }

    /**
     * تأیید سند — ایجاد stock_transactions برای هر ردیف
     */
    public function approve(WarehouseDocument $doc, int $approverId): void
    {
        if ($doc->status !== WarehouseDocument::STATUS_PENDING) {
            throw new \RuntimeException('تنها اسناد در انتظار تأیید قابل تصویب هستند.');
        }

        DB::transaction(function () use ($doc, $approverId) {

            foreach ($doc->items as $item) {
                $this->createTransactionsForItem($doc, $item);
            }

            $doc->update([
                'status'      => WarehouseDocument::STATUS_APPROVED,
                'approved_by' => $approverId,
                'approved_at' => now(),
            ]);
        });
    }

    /**
     * رد سند
     */
    public function reject(WarehouseDocument $doc, int $rejectorId, ?string $reason = null): void
    {
        if ($doc->status !== WarehouseDocument::STATUS_PENDING) {
            throw new \RuntimeException('تنها اسناد در انتظار تأیید قابل رد هستند.');
        }

        $doc->update([
            'status'           => WarehouseDocument::STATUS_REJECTED,
            'rejected_at'      => now(),
            'rejection_reason' => $reason,
        ]);
    }

    /**
     * لغو سند تأیید‌شده — معکوس کردن تراکنش‌ها
     */
    public function cancel(WarehouseDocument $doc): void
    {
        if ($doc->status !== WarehouseDocument::STATUS_APPROVED) {
            throw new \RuntimeException('تنها اسناد تأیید‌شده قابل لغو هستند.');
        }

        DB::transaction(function () use ($doc) {
            // معکوس کردن: حذف tran های مرتبط
            $txIds = $doc->items->pluck('stock_transaction_id')->filter();
            if ($txIds->isNotEmpty()) {
                StockTransaction::whereIn('id', $txIds)->delete();
            }

            // ریست ارجاع به tran در ردیف‌ها
            WarehouseDocumentItem::where('warehouse_document_id', $doc->id)
                ->update(['stock_transaction_id' => null]);

            $doc->update(['status' => WarehouseDocument::STATUS_CANCELLED]);
        });
    }

    // ─── private ─────────────────────────────────────────────────────────────

    private function createTransactionsForItem(WarehouseDocument $doc, WarehouseDocumentItem $item): void
    {
        $base = [
            'tenant_id'             => $doc->tenant_id,
            'company_id'            => $doc->company_id,
            'user_id'               => $doc->created_by,
            'warehouse_id'          => $doc->warehouse_id,
            'warehouse_location_id' => $item->warehouse_location_id ?? $doc->warehouse_location_id,
            'product_id'            => $item->product_id,
            'quantity'              => $item->quantity,
            'unit_price'            => $item->unit_price,
            'measurement_unit_id'   => $item->measurement_unit_id,
            'serial_number'         => $item->serial_number,
            'batch_number'          => $item->batch_number,
            'expiry_date'           => $item->expiry_date,
            'fiscal_year_id'        => $doc->fiscal_year_id,
            'cost_center_id'        => $doc->cost_center_id,
            'description'           => "سند {$doc->document_number}",
            'status'                => InventoryTransactionStatus::APPROVED->value,
        ];

        $transactions = match ($doc->type) {
            WarehouseDocument::TYPE_RECEIPT,
            WarehouseDocument::TYPE_RETURN_IN  => $this->buildInbound($base, $doc),
            WarehouseDocument::TYPE_ISSUE,
            WarehouseDocument::TYPE_RETURN_OUT => $this->buildOutbound($base, $doc),
            WarehouseDocument::TYPE_TRANSFER   => $this->buildTransfer($base, $doc, $item),
            WarehouseDocument::TYPE_ADJUSTMENT => $this->buildAdjustment($base, $item),
            default                            => [],
        };

        foreach ($transactions as $txData) {
            $tx = StockTransaction::create($txData);
            // ارجاع اولین tran به ردیف سند
            if (!isset($mainTxId)) {
                $item->update(['stock_transaction_id' => $tx->id]);
            }
        }
    }

    private function buildInbound(array $base, WarehouseDocument $doc): array
    {
        $type = $doc->type === WarehouseDocument::TYPE_RETURN_IN
            ? InventoryTransactionType::RETURN_SALE   // مرجوعی از مشتری
            : InventoryTransactionType::PURCHASE;     // رسید خرید / ورود به انبار

        return [array_merge($base, ['type' => $type->value])];
    }

    private function buildOutbound(array $base, WarehouseDocument $doc): array
    {
        $type = $doc->type === WarehouseDocument::TYPE_RETURN_OUT
            ? InventoryTransactionType::RETURN_PURCHASE  // مرجوعی به تأمین‌کننده
            : InventoryTransactionType::SALE;            // حواله خروج از انبار

        return [array_merge($base, ['type' => $type->value])];
    }

    private function buildTransfer(array $base, WarehouseDocument $doc, WarehouseDocumentItem $item): array
    {
        // خروج از انبار مبدأ + ورود به انبار مقصد
        return [
            array_merge($base, ['type' => InventoryTransactionType::TRANSFER_OUT->value]),
            array_merge($base, [
                'type'         => InventoryTransactionType::TRANSFER_IN->value,
                'warehouse_id' => $doc->destination_warehouse_id,
                'warehouse_location_id' => null,
            ]),
        ];
    }

    private function buildAdjustment(array $base, WarehouseDocumentItem $item): array
    {
        // مقدار مثبت = افزایش، منفی = کاهش
        $type = (float)$item->quantity >= 0
            ? InventoryTransactionType::ADJUSTMENT_IN
            : InventoryTransactionType::ADJUSTMENT_OUT;

        return [array_merge($base, [
            'type'     => $type->value,
            'quantity' => abs((float)$item->quantity),
        ])];
    }
}
