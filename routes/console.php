<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Jobs\SyncFuelConsumedJob;
use App\Jobs\SyncVehicleActivityJob;
use App\Jobs\SyncFuelFillJob;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Mengeksekusi penarikan API Fuel Consumed (H-1) via Job Queue setiap jam 01:00 pagi setiap harinya
//Schedule::job(new SyncFuelConsumedJob)->dailyAt('01:00')->withoutOverlapping();

// Mengeksekusi penarikan API Activity (H-1) via Job Queue setiap jam 01:30 pagi setiap harinya
//Schedule::job(new SyncVehicleActivityJob)->dailyAt('01:30')->withoutOverlapping();

// Mengeksekusi penarikan API Fuel Fill (H-1) via Job Queue setiap jam 02:00 pagi setiap harinya
//Schedule::job(new SyncFuelFillJob)->dailyAt('02:00')->withoutOverlapping();

// Mengeksekusi penarikan API Trips (H-1) via Job Queue setiap jam 02:30 pagi setiap harinya
//Schedule::job(new \App\Jobs\SyncTripsJob)->dailyAt('02:30')->withoutOverlapping();

// Command ini akan berjalan setiap hari pada jam 01:00 Pagi
// dan secara otomatis menarik data H-1 berkat konfigurasi di Langkah 1
Schedule::command('cartrack:sync-fuel')->dailyAt('08:15')->withoutOverlapping();