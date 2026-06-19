<?php

namespace App\Observers;

use App\Concerns\ChecksSubscriptionLimit;
use App\Models\Company;

class CompanyObserver
{
    use ChecksSubscriptionLimit;

    public function creating(Company $company): void
    {
        $this->checkLimit('max_organizations', $company);
    }

    public function created(Company $company): void
    {
        $this->incrementUsage('max_organizations', $company);
    }
}