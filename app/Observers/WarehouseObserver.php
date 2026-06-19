<?php

namespace App\Observers;

use App\Concerns\ChecksSubscriptionLimit;
use App\Models\Warehouse;

class WarehouseObserver
{
    use ChecksSubscriptionLimit;

    public function creating(Warehouse $warehouse): void
    {
        $this->checkLimit('max_warehouses', $warehouse);
    }

    public function created(Warehouse $warehouse): void
    {
        $this->incrementUsage('max_warehouses', $warehouse);
    }
}