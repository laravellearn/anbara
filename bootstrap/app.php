<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    // bootstrap/app.php
    ->withSchedule(function (\Illuminate\Console\Scheduling\Schedule $schedule) {
        $schedule->job(new \App\Jobs\HandleExpiredTrials)->daily();
        $schedule->command('subscriptions:notify-expiring --days=7')->dailyAt('08:00');
        $schedule->command('subscriptions:notify-expiring --days=1')->dailyAt('09:00');
    })
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            \App\Http\Middleware\SetTenantContext::class,
            \App\Http\Middleware\SetCompanyContext::class,   // ← حتماً این را بیفزایید

        ]);
        $middleware->alias([
            'require.tenant' => \App\Http\Middleware\RequireTenant::class,
            'can' => \App\Http\Middleware\CheckPermission::class,
            'superadmin' => \App\Http\Middleware\EnsureSuperAdmin::class,
            'owner' => \App\Http\Middleware\IsOwner::class,
            'check.subscription' => \App\Http\Middleware\CheckSubscription::class,


        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn(Request $request) => $request->is('api/*'),
        );
    })->create();
