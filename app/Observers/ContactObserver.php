<?php

namespace App\Observers;

use App\Concerns\ChecksSubscriptionLimit;
use App\Models\Contact;

class ContactObserver
{
    use ChecksSubscriptionLimit;

    public function creating(Contact $contact): void
    {
        // فقط تأمین‌کنندگان محدودیت دارند
        if (!in_array($contact->type, ['supplier', 'both'])) return;

        $this->checkLimit('max_suppliers', $contact);
    }

    public function created(Contact $contact): void
    {
        if (!in_array($contact->type, ['supplier', 'both'])) return;

        $this->incrementUsage('max_suppliers', $contact);
    }
}