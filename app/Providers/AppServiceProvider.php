<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Load helper functions
        require_once app_path('Helpers/helpers.php');
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Set locale to Indonesian for Carbon dates
        Carbon::setLocale('id');

        // Register View Composer for Sidebar
        View::composer('components.sidebar', \App\Http\ViewComposers\SidebarComposer::class);
    }
}
