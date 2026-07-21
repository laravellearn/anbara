<?php

namespace App\Mail;

use App\Models\Subscription;
use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SubscriptionExpiringMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Tenant       $tenant,
        public Subscription $subscription,
        public int          $remainDays,
    ) {}

    public function envelope(): Envelope
    {
        $planName = $this->subscription->plan?->name ?? 'اشتراک';
        return new Envelope(
            subject: "⚠️ هشدار: {$planName} شما ظرف {$this->remainDays} روز منقضی می‌شود",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.subscription-expiring',
        );
    }
}
