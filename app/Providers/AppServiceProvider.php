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

        // Register View Composer for the insured sidebar (now embedded in template)
        \Illuminate\Support\Facades\View::composer(
            'assure.layouts.template',
            \App\Http\View\Composers\AssureSidebarComposer::class
        );

        // Register View Composer for the assurance sidebar
        \Illuminate\Support\Facades\View::composer(
            'assurance.layouts.sidebar',
            \App\Http\View\Composers\AssuranceSidebarComposer::class
        );

        // Register View Composer for the personnel sidebar
        \Illuminate\Support\Facades\View::composer(
            'personnel.layouts.sidebar',
            \App\Http\View\Composers\PersonnelSidebarComposer::class
        );

        // Register View Composer for the groupe navbar
        \Illuminate\Support\Facades\View::composer(
            'groupe.layouts.navbar',
            \App\Http\View\Composers\GroupeNavbarComposer::class
        );
    }
}
