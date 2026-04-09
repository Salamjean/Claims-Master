<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);

        // Register View Composer for the insured sidebar
        \Illuminate\Support\Facades\View::composer(
            'assure.layouts.sidebar', 
            \App\Http\View\Composers\AssureSidebarComposer::class
        );
    }
}
