<?php

namespace App\Services;

use App\Enums\InventoryTransactionType;
use App\Models\FiscalYear;
use App\Models\StockTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FiscalYearClosingService
{
    /**
     * بستن سال مالی با انتقال موجودی به سال جدید
     *
     * 1. بررسی: سال باید باز باشد
     * 2. بررسی: تراکنش‌های معلق (draft) وجود نداشته باشد
     * 3. محاسبه موجودی پایان دوره هر کالا در هر انبار
     * 4. ایجاد رکوردهای opening balance در سال مالی جدید (در صورت وجود)
     * 5. بستن سال مالی (is_closed = true)
     */
    public function close(FiscalYear $fiscalYear, bool $carryForward = true): array
    {
        if ($fiscalYear->is_closed) {
            return ['success' => false, 'message' => 'سال مالی قبلاً بسته شده است.'];
        }

        DB::beginTransaction();
        try {
            // ─── بررسی تراکنش‌های معلق ───────────────────────────────────────
            $pendingCount = StockTransaction::where('tenant_id',    $fiscalYear->tenant_id)
                ->where('company_id',   $fiscalYear->company_id)
                ->where('fiscal_year_id', $fiscalYear->id)
                ->whereIn('status', ['draft', 'pending'])
                ->count();

            if ($pendingCount > 0) {
                return [
                    'success' => false,
                    'message' => "تعداد {$pendingCount} تراکنش تأییدنشده وجود دارد. لطفاً ابتدا آن‌ها را تأیید یا لغو کنید.",
                ];
            }

            // ─── carry-forward موجودی به سال جدید ───────────────────────────
            $carryForwardCount = 0;
            if ($carryForward) {
                $nextFY = FiscalYear::where('tenant_id',  $fiscalYear->tenant_id)
                    ->where('company_id', $fiscalYear->company_id)
                    ->where('start_date', '>', $fiscalYear->end_date)
                    ->where('is_closed', false)
                    ->orderBy('start_date')
                    ->first();

                if ($nextFY) {
                    // محاسبه موجودی پایان دوره: خلاصه approved تراکنش‌های این سال
                    $balances = StockTransaction::where('tenant_id',    $fiscalYear->tenant_id)
                        ->where('company_id',   $fiscalYear->company_id)
                        ->where('fiscal_year_id', $fiscalYear->id)
                        ->where('status', 'approved')
                        ->selectRaw('product_id, warehouse_id, measurement_unit_id,
                            SUM(CASE WHEN direction = "in"  THEN quantity ELSE 0 END)
                          - SUM(CASE WHEN direction = "out" THEN quantity ELSE 0 END) AS net_qty,
                            AVG(unit_cost) as avg_cost')
                        ->groupBy('product_id', 'warehouse_id', 'measurement_unit_id')
                        ->having('net_qty', '>', 0)
                        ->get();

                    foreach ($balances as $row) {
                        // جلوگیری از تکراری شدن opening balance در سال جدید
                        $exists = StockTransaction::where('tenant_id',    $fiscalYear->tenant_id)
                            ->where('company_id',   $fiscalYear->company_id)
                            ->where('fiscal_year_id', $nextFY->id)
                            ->where('type', InventoryTransactionType::OPENING)
                            ->where('product_id',   $row->product_id)
                            ->where('warehouse_id', $row->warehouse_id)
                            ->exists();

                        if (! $exists) {
                            StockTransaction::create([
                                'tenant_id'           => $fiscalYear->tenant_id,
                                'company_id'          => $fiscalYear->company_id,
                                'fiscal_year_id'      => $nextFY->id,
                                'type'                => InventoryTransactionType::OPENING,
                                'direction'           => 'in',
                                'status'              => 'approved',
                                'product_id'          => $row->product_id,
                                'warehouse_id'        => $row->warehouse_id,
                                'measurement_unit_id' => $row->measurement_unit_id,
                                'quantity'            => $row->net_qty,
                                'unit_cost'           => $row->avg_cost ?? 0,
                                'transaction_date'    => $nextFY->start_date,
                                'reference_type'      => 'year_end_closing',
                                'reference_id'        => $fiscalYear->id,
                                'description'         => 'موجودی انتقالی از سال مالی ' . $fiscalYear->name,
                                'created_by'          => auth()->id(),
                            ]);
                            $carryForwardCount++;
                        }
                    }
                }
            }

            // ─── بستن سال مالی ───────────────────────────────────────────────
            $fiscalYear->update([
                'is_closed' => true,
                'is_active' => false,
                'closed_at' => now(),
                'closed_by' => auth()->id(),
            ]);

            DB::commit();

            return [
                'success'             => true,
                'carry_forward_count' => $carryForwardCount,
                'message'             => "سال مالی «{$fiscalYear->name}» با موفقیت بسته شد." .
                    ($carryForwardCount > 0 ? " {$carryForwardCount} ردیف موجودی به سال بعد منتقل شد." : ''),
            ];
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('FiscalYearClosingService::close', [
                'fiscal_year_id' => $fiscalYear->id,
                'error'          => $e->getMessage(),
            ]);
            return ['success' => false, 'message' => 'خطای سیستمی: ' . $e->getMessage()];
        }
    }
}
