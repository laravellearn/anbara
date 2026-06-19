<?php

namespace App\Observers;

use App\Concerns\ChecksSubscriptionLimit;
use App\Models\User;
use App\Services\TenantManager;

class UserObserver
{
    use ChecksSubscriptionLimit;

    public function creating(User $user): void
    {
        // فقط زمانی چک کن که کاربر tenant داشته باشد (یعنی مدیر سازمان کاربر جدید می‌سازد، نه ثبت‌نام اولیه)
        if (!$user->tenant_id) return;

        $this->checkLimit('max_users', $user);
    }

    public function created(User $user): void
    {
        if (!$user->tenant_id) return;

        $this->incrementUsage('max_users', $user);
    }
}