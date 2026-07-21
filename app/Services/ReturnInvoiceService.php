<?php

namespace App\Services;

use App\Enums\InventoryTransactionType;
use App\Models\FiscalYear;
use App\Models\InvoicePayment;
use App\Models\ReturnInvoice;
use App\Models\ReturnInvoiceItem;
use App\Models\SalesInvoice;
use App\Models\PurchaseInvoice;
use App\Models\StockTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ReturnInvoiceService
{
    /**
     * ایجاد سند برگشت و برگشت موجودی به انبار
     */
    public function create(array $data, array $items): ReturnInvoice
    {
        DB::beginTransaction();
        try {
            // شماره خودکار
            $data['return_number'] = $this->generateNumber($data['tenant_id'], $data['type']);
            $data['created_by']    = auth()->id();

            $returnInvoice = ReturnInvoice::create($data);

            $subtotal = 0;
            foreach ($items as $idx => $itemData) {
                $lineTotal = round(
                    $itemData['quantity'] * $itemData['unit_price']
                    * (1 - ($itemData['discount_percent'] ?? 0) / 100),
                    4
                );
                $itemData['line_total']  = $lineTotal;
                $itemData['sort_order']  = $idx;
                $subtotal += $lineTotal;

                $returnInvoice->items()->create($itemData);
            }

            // محاسبه جمع‌ها
            $discountAmount = $data['discount_amount'] ?? 0;
            $taxPercent     = $data['tax_percent']    ?? 9;
            $taxAmount      = round(($subtotal - $discountAmount) * $taxPercent / 100, 4);
            $totalAmount    = $subtotal - $discountAmount + $taxAmount;

            $returnInvoice->update([
                'subtotal'        => $subtotal,
                'discount_amount' => $discountAmount,
                'tax_amount'      => $taxAmount,
                'total_amount'    => $totalAmount,
            ]);

            DB::commit();
            return $returnInvoice->fresh('items');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('ReturnInvoiceService::create', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * تأیید سند برگشت — اعمال حرکت موجودی
     */
    public function confirm(ReturnInvoice $returnInvoice): ReturnInvoice
    {
        if ($returnInvoice->isConfirmed()) {
            throw new \LogicException('سند برگشت قبلاً تأیید شده است.');
        }

        DB::beginTransaction();
        try {
            // جهت حرکت موجودی:
            // برگشت از فروش → ورود به انبار (in)
            // برگشت از خرید → خروج از انبار (out)
            $direction = $returnInvoice->type === 'sales' ? 'in' : 'out';
            $txType    = $returnInvoice->type === 'sales'
                ? InventoryTransactionType::RETURN_SALE
                : InventoryTransactionType::RETURN_PURCHASE;

            foreach ($returnInvoice->items as $item) {
                StockTransaction::create([
                    'tenant_id'           => $returnInvoice->tenant_id,
                    'company_id'          => $returnInvoice->company_id,
                    'fiscal_year_id'      => $returnInvoice->fiscal_year_id,
                    'type'                => $txType,
                    'direction'           => $direction,
                    'status'              => 'approved',
                    'product_id'          => $item->product_id,
                    'warehouse_id'        => $returnInvoice->warehouse_id,
                    'measurement_unit_id' => $item->measurement_unit_id,
                    'quantity'            => $item->quantity,
                    'unit_cost'           => $item->unit_price,
                    'transaction_date'    => $returnInvoice->return_date,
                    'reference_type'      => 'return_invoice',
                    'reference_id'        => $returnInvoice->id,
                    'description'         => 'برگشت سند ' . $returnInvoice->return_number,
                    'created_by'          => auth()->id(),
                ]);
            }

            $returnInvoice->update([
                'status'       => 'confirmed',
                'confirmed_by' => auth()->id(),
                'confirmed_at' => now(),
            ]);

            // به‌روزرسانی وضعیت فاکتور اصلی (اختیاری)
            $this->updateOriginalInvoiceStatus($returnInvoice);

            DB::commit();
            return $returnInvoice->fresh();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('ReturnInvoiceService::confirm', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * لغو سند برگشت — معکوس کردن حرکت موجودی
     */
    public function cancel(ReturnInvoice $returnInvoice): void
    {
        if (! $returnInvoice->isConfirmed()) {
            $returnInvoice->update(['status' => 'cancelled']);
            return;
        }

        DB::beginTransaction();
        try {
            // حذف تراکنش‌های انبار مرتبط
            StockTransaction::where('reference_type', 'return_invoice')
                ->where('reference_id', $returnInvoice->id)
                ->delete();

            $returnInvoice->update(['status' => 'cancelled']);
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    // ─── Private Helpers ──────────────────────────────────────────────────────

    private function generateNumber(int $tenantId, string $type): string
    {
        $prefix = $type === 'sales' ? 'RS' : 'RP';
        $year   = now()->format('y');
        $last   = ReturnInvoice::where('tenant_id', $tenantId)
            ->where('type', $type)
            ->whereYear('created_at', now()->year)
            ->count() + 1;
        return sprintf('%s-%s-%04d', $prefix, $year, $last);
    }

    private function updateOriginalInvoiceStatus(ReturnInvoice $returnInvoice): void
    {
        if ($returnInvoice->type === 'sales' && $returnInvoice->sales_invoice_id) {
            // می‌توان وضعیت فاکتور فروش را بر اساس مقدار برگشت‌شده به‌روز کرد
            // فعلاً فقط log می‌کنیم
            Log::info('ReturnInvoice confirmed for SalesInvoice', [
                'sales_invoice_id' => $returnInvoice->sales_invoice_id,
                'return_total'     => $returnInvoice->total_amount,
            ]);
        }
    }
}
