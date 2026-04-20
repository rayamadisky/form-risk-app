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
        // Maksa Laravel pakai HTTPS kalau bukan di XAMPP lokal
        if (config('app.env') !== 'local') {
            URL::forceScheme('https');
        }
    }
}