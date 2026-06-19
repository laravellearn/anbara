<?php

namespace App\Observers;

use App\Concerns\ChecksSubscriptionLimit;
use App\Models\Category;

class CategoryObserver
{
    use ChecksSubscriptionLimit;

    public function creating(Category $category): void
    {
        $this->checkLimit('max_categories', $category);
    }

    public function created(Category $category): void
    {
        $this->incrementUsage('max_categories', $category);
    }
}