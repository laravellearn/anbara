<?php

namespace App\Mail;

use App\Models\PurchaseInvoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PurchaseInvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public PurchaseInvoice $invoice) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'فاکتور خرید شماره ' . $this->invoice->invoice_number . ' — ' . config('app.name'),
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.purchase-invoice');
    }
}
