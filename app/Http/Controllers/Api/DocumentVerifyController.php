<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PurchaseInvoice;
use App\Models\WarehouseDocument;
use App\Services\DigitalSignatureService;
use Illuminate\Http\Request;

class DocumentVerifyController extends Controller
{
    public function __construct(private DigitalSignatureService $signer) {}

    /**
     * نمایش صفحه اصالت‌سنجی QR Code (عمومی — بدون login)
     */
    public function show(string $uuid)
    {
        // جستجو در اسناد انبار
        $doc = WarehouseDocument::where('verify_uuid', $uuid)->first();
        if ($doc) {
            $valid  = $this->signer->verifyDocument($doc);
            $type   = 'warehouse_document';
            $record = $doc;
            return view('verify.document', compact('valid', 'type', 'record', 'uuid'));
        }

        // جستجو در فاکتورها
        $invoice = PurchaseInvoice::where('verify_uuid', $uuid)->first();
        if ($invoice) {
            $valid  = $this->signer->verifyInvoice($invoice);
            $type   = 'purchase_invoice';
            $record = $invoice;
            return view('verify.document', compact('valid', 'type', 'record', 'uuid'));
        }

        // یافت نشد
        return view('verify.not-found', compact('uuid'));
    }
}
