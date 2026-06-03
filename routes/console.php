<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Services\CartrackFuelService;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Mengeksekusi penarikan API setiap jam 01:00 pagi setiap harinya
Schedule::call(function () {
    app(CartrackFuelService::class)->syncFuelData();
})->dailyAt('01:00')->name('sync_cartrack_fuel')->withoutOverlapping();