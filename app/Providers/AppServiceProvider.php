<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

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
        // Force application URL for subdirectory deployments
        if ($appUrl = config('app.url')) {
            URL::forceRootUrl($appUrl);
        }

        // Force HTTPS scheme if APP_URL uses https
        if (str_contains(config('app.url', ''), 'https://')) {
            URL::forceScheme('https');
        }
    }

}
