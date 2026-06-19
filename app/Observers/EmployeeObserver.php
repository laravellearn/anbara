<?php

namespace App\Observers;

use App\Concerns\ChecksSubscriptionLimit;
use App\Models\Employee;

class EmployeeObserver
{
    use ChecksSubscriptionLimit;

    public function creating(Employee $employee): void
    {
        $this->checkLimit('max_employees', $employee);
    }

    public function created(Employee $employee): void
    {
        $this->incrementUsage('max_employees', $employee);
    }
}