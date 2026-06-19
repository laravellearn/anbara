<?php

namespace App\Observers;

use App\Concerns\ChecksSubscriptionLimit;
use App\Models\Product;

class ProductObserver
{
    use ChecksSubscriptionLimit;

    public function creating(Product $product): void
    {
        $this->checkLimit('max_products', $product);
    }

    public function created(Product $product): void
    {
        $this->incrementUsage('max_products', $product);
    }
}