<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;

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

            // Configure Livewire for subdirectory
            $path = parse_url($appUrl, PHP_URL_PATH);
            if ($path && $path !== '/') {
                Livewire::setUpdateRoute(function ($handle) use ($path) {
                    return \Illuminate\Support\Facades\Route::post($path . '/livewire/update', $handle);
                });
            }
        }

        // Force HTTPS scheme if APP_URL uses https
        if (str_contains(config('app.url', ''), 'https://')) {
            URL::forceScheme('https');
        }
    }

}
