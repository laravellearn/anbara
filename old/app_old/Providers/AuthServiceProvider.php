<?php

namespace App\Providers;

use App\Models\Permission;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [];

    public function boot()
    {
        $this->registerPolicies();

        // تمام مجوزها را از دیتابیس بگیرید (فقط در صورتی که جدول وجود دارد)
        if (\Schema::hasTable('permissions')) {
            $permissions = Permission::all();
            foreach ($permissions as $permission) {
                Gate::define($permission->title, function ($user) use ($permission) {
                    return $user->hasPermission($permission);
                });
            }
        }
    }
}