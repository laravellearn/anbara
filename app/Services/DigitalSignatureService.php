<?php

namespace App\Services;

use App\Models\WarehouseDocument;
use App\Models\PurchaseInvoice;
use Illuminate\Support\Str;

class DigitalSignatureService
{
    /**
     * امضای دیجیتال یک سند انبار
     */
    public function signDocument(WarehouseDocument $doc): WarehouseDocument
    {
        $uuid      = (string) Str::uuid();
        $signature = $this->computeSignature('warehouse_document', $doc->id, $uuid);

        $doc->update([
            'verify_uuid'       => $uuid,
            'digital_signature' => $signature,
            'signed_at'         => now(),
            'signed_by'         => auth()->id(),
        ]);

        return $doc->fresh();
    }

    /**
     * امضای دیجیتال یک فاکتور خرید
     */
    public function signInvoice(PurchaseInvoice $invoice): PurchaseInvoice
    {
        $uuid      = (string) Str::uuid();
        $signature = $this->computeSignature('purchase_invoice', $invoice->id, $uuid);

        $invoice->update([
            'verify_uuid'       => $uuid,
            'digital_signature' => $signature,
            'signed_at'         => now(),
            'signed_by'         => auth()->id(),
        ]);

        return $invoice->fresh();
    }

    /**
     * اعتبارسنجی امضای سند
     */
    public function verifyDocument(WarehouseDocument $doc): bool
    {
        if (! $doc->verify_uuid || ! $doc->digital_signature) return false;
        $expected = $this->computeSignature('warehouse_document', $doc->id, $doc->verify_uuid);
        return hash_equals($expected, $doc->digital_signature);
    }

    /**
     * اعتبارسنجی امضای فاکتور
     */
    public function verifyInvoice(PurchaseInvoice $invoice): bool
    {
        if (! $invoice->verify_uuid || ! $invoice->digital_signature) return false;
        $expected = $this->computeSignature('purchase_invoice', $invoice->id, $invoice->verify_uuid);
        return hash_equals($expected, $invoice->digital_signature);
    }

    private function computeSignature(string $type, int $id, string $uuid): string
    {
        $secret = config('app.key');
        return hash_hmac('sha256', "{$type}:{$id}:{$uuid}", $secret);
    }
}
