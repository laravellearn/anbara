<?php

namespace App\Observers;

use App\Concerns\ChecksSubscriptionLimit;
use App\Models\Brand;

class BrandObserver
{
    use ChecksSubscriptionLimit;

    public function creating(Brand $brand): void
    {
        $this->checkLimit('max_brands', $brand);
    }

    public function created(Brand $brand): void
    {
        $this->incrementUsage('max_brands', $brand);
    }
}