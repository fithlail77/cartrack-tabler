<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

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
        // 2. Beritahu Laravel untuk menggunakan view Bootstrap untuk pagination
        Paginator::useBootstrapFive(); 
        
        // Catatan: Jika template Tabler yang Anda gunakan adalah versi lama (Bootstrap 4),
        // silakan ganti menjadi Paginator::useBootstrapFour();
    }
}
