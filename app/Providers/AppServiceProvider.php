<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use App\Models\Setting;
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

        // Runtime configuration for Email and App Name from database
        if (!app()->runningInConsole() && Schema::hasTable('settings')) {
            // Get all relevant settings at once
            $settings = Setting::whereIn('group', ['email', 'general'])->get()->pluck('value', 'key');
            
            if ($settings->isNotEmpty()) {
                config([
                    // App Name binding
                    'app.name' => $settings->get('app_name', config('app.name')),

                    // SMTP binding
                    'mail.mailers.smtp.host' => $settings->get('mail_host', config('mail.mailers.smtp.host')),
                    'mail.mailers.smtp.port' => $settings->get('mail_port', config('mail.mailers.smtp.port')),
                    'mail.mailers.smtp.username' => $settings->get('mail_username', config('mail.mailers.smtp.username')),
                    'mail.mailers.smtp.password' => $settings->get('mail_password', config('mail.mailers.smtp.password')),
                    'mail.mailers.smtp.encryption' => $settings->get('mail_encryption', config('mail.mailers.smtp.encryption')),
                    'mail.from.address' => $settings->get('mail_from_address', config('mail.from.address')),
                    'mail.from.name' => $settings->get('mail_from_name', config('mail.from.name')),
                ]);
            }
        }

        // Register View Composer for Sidebar
        View::composer('components.sidebar', \App\Http\ViewComposers\SidebarComposer::class);
    }
}
