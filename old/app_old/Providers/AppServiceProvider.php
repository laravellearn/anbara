<?php

namespace App\Providers;

use App\Models\License;
use App\View\Composers\SubscriptionComposer;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        view()->composer('*',function($view) {
            $view->with('userLogin', \Auth::user());
        });
        View::composer('layouts.master', SubscriptionComposer::class);
    }
}
