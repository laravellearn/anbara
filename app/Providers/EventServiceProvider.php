<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use App\Models\ActivityLog;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        // می‌توانید از اینجا استفاده کنید، ولی روش زیر گویاتر است
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        parent::boot();

        \Event::listen(Login::class, function ($event) {
            ActivityLog::create([
                'tenant_id'   => $event->user->tenant_id ? : '',
                'user_id'     => $event->user->id,
                'action'      => 'login',
                'subject_type'=> 'User',
                'subject_id'  => $event->user->id,
                'ip_address'  => request()->ip(),
                'description' => 'کاربر وارد سامانه شد.',
            ]);
        });

        \Event::listen(Logout::class, function ($event) {
            ActivityLog::create([
                'tenant_id'   => $event->user->tenant_id ? : '',
                'user_id'     => $event->user->id,
                'action'      => 'logout',
                'subject_type'=> 'User',
                'subject_id'  => $event->user->id,
                'ip_address'  => request()->ip(),
                'description' => 'کاربر از سامانه خارج شد.',
            ]);
        });
    }
}