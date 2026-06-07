<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Jobs\SyncFuelConsumedJob;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Mengeksekusi penarikan API (H-1) via Job Queue setiap jam 01:00 pagi setiap harinya
Schedule::job(new SyncFuelConsumedJob)->dailyAt('01:00')->withoutOverlapping();