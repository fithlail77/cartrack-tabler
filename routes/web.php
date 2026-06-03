<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VehicleActivityController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\FuelConsumedController;

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    // Rute untuk aktivitas kendaraan
    Route::get('/cartrack/activity', [VehicleActivityController::class, 'index'])->name('cartrack.activity.index');
    Route::post('/cartrack/activity/sync', [VehicleActivityController::class, 'sync'])->name('cartrack.activity.sync');
    // Rute untuk data kenderaan
    Route::get('/vehicles', [VehicleController::class, 'index'])->name('vehicles.index');
    Route::post('/vehicles/sync', [VehicleController::class, 'syncApi'])->name('vehicles.sync');
    // Rute untuk data konsumsi bahan bakar Bulk
    Route::get('/fuel-consumed', [FuelConsumedController::class, 'index'])->name('fuels.index');
    Route::post('/fuel-consumed/sync', [FuelConsumedController::class, 'syncApi'])->name('fuels.sync');
});

require __DIR__.'/auth.php';


