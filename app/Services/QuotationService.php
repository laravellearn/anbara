<?php

namespace App\Services;

use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\SalesInvoice;
use App\Models\SalesInvoiceItem;
use Illuminate\Support\Facades\DB;

class QuotationService
{
    public function createOrUpdate(array $data, int $tenantId, int $companyId, int $userId, ?Quotation $quotation = null): Quotation
    {
        return DB::transaction(function () use ($data, $tenantId, $companyId, $userId, $quotation) {
            $items = $data['items'] ?? [];
            unset($data['items']);

            // محاسبه مبالغ
            $subtotal = 0;
            foreach ($items as &$item) {
                $item['total_price'] = max(0, ($item['quantity'] * $item['unit_price']) - ($item['discount_amount'] ?? 0));
                $subtotal += $item['total_price'];
            }
            unset($item);

            $discPct  = (float)($data['discount_percent'] ?? 0);
            $discAmt  = round($subtotal * $discPct / 100, 2);
            $after    = $subtotal - $discAmt;
            $taxPct   = (float)($data['tax_percent'] ?? 9);
            $taxAmt   = round($after * $taxPct / 100, 2);
            $total    = $after + $taxAmt;

            $payload = array_merge($data, [
                'tenant_id'       => $tenantId,
                'company_id'      => $companyId,
                'created_by'      => $userId,
                'subtotal'        => $subtotal,
                'discount_amount' => $discAmt,
                'tax_amount'      => $taxAmt,
                'total_amount'    => $total,
            ]);

            if ($quotation) {
                $quotation->update($payload);
                $quotation->items()->delete();
            } else {
                $quotation = Quotation::create($payload);
            }

            foreach ($items as $idx => $item) {
                QuotationItem::create(array_merge($item, [
                    'quotation_id' => $quotation->id,
                    'sort_order'   => $idx,
                ]));
            }

            return $quotation->fresh('items');
        });
    }

    /** تبدیل پیش‌فاکتور به فاکتور فروش */
    public function convertToInvoice(Quotation $quotation): SalesInvoice
    {
        return DB::transaction(function () use ($quotation) {
            $invoice = SalesInvoice::create([
                'tenant_id'        => $quotation->tenant_id,
                'company_id'       => $quotation->company_id,
                'customer_id'      => $quotation->customer_id,
                'warehouse_id'     => $quotation->warehouse_id,
                'fiscal_year_id'   => $quotation->fiscal_year_id,
                'cost_center_id'   => $quotation->cost_center_id,
                'invoice_date'     => now()->toDateString(),
                'due_date'         => $quotation->valid_until,
                'reference_number' => $quotation->quotation_number,
                'description'      => $quotation->description,
                'status'           => SalesInvoice::STATUS_DRAFT,
                'subtotal'         => $quotation->subtotal,
                'discount_percent' => $quotation->discount_percent,
                'discount_amount'  => $quotation->discount_amount,
                'tax_percent'      => $quotation->tax_percent,
                'tax_amount'       => $quotation->tax_amount,
                'total_amount'     => $quotation->total_amount,
                'paid_amount'      => 0,
                'created_by'       => auth()->id(),
            ]);

            foreach ($quotation->items as $item) {
                SalesInvoiceItem::create([
                    'sales_invoice_id'    => $invoice->id,
                    'product_id'          => $item->product_id,
                    'measurement_unit_id' => $item->measurement_unit_id,
                    'quantity'            => $item->quantity,
                    'unit_price'          => $item->unit_price,
                    'discount_amount'     => $item->discount_amount,
                    'total_price'         => $item->total_price,
                    'description'         => $item->description,
                    'sort_order'          => $item->sort_order,
                ]);
            }

            $quotation->update([
                'status'          => Quotation::STATUS_CONVERTED,
                'sales_invoice_id'=> $invoice->id,
            ]);

            return $invoice;
        });
    }
}
