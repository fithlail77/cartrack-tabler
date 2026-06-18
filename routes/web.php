<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Models\Vehicle;
use App\Models\FuelConsumedLevel;
use App\Models\FuelFill;
use App\Models\CartrackTrip;
use App\Models\VehicleActivity;
use Carbon\Carbon;
use App\Http\Controllers\VehicleActivityController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\FuelConsumedController;
use App\Http\Controllers\FuelFillController;
use App\Http\Controllers\TripSyncController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\ActivityReportController;

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/dashboard', function () {
    $startOfMonth = Carbon::now()->startOfMonth();
    $endOfMonth = Carbon::now()->endOfMonth();

    $totalVehicles = Vehicle::count();
    
    $totalFuelConsumed = FuelConsumedLevel::whereBetween('start_period_timestamp', [$startOfMonth, $endOfMonth])
        ->sum('estimated_fuel_used');

    $totalFuelFills = FuelFill::whereBetween('fill_timestamp', [$startOfMonth, $endOfMonth])
        ->sum('fill_ammount_litres');

    $totalDistanceMeters = CartrackTrip::whereBetween('start_timestamp', [$startOfMonth, $endOfMonth])
        ->sum('trip_distance');

    $totalDistance = $totalDistanceMeters / 1000;

    $totalDrivingHours = VehicleActivity::whereBetween('activity_date', [$startOfMonth, $endOfMonth])
        ->sum('driving_time_seconds') / 3600;

    $totalIdleHours = VehicleActivity::whereBetween('activity_date', [$startOfMonth, $endOfMonth])
        ->sum('idle_time_seconds') / 3600;

    // Data untuk Grafik Fuel Consumed Monthly (Tahun Berjalan)
    $currentYear = Carbon::now()->year;
    $monthlyFuelRaw = FuelConsumedLevel::selectRaw('EXTRACT(MONTH FROM start_period_timestamp) as month, SUM(estimated_fuel_used) as total')
        ->whereYear('start_period_timestamp', $currentYear)
        ->groupBy('month')
        ->orderBy('month')
        ->get();

    $chartLabels = [];
    $chartData = [];
    for ($m = 1; $m <= 12; $m++) {
        $chartLabels[] = Carbon::create()->month($m)->translatedFormat('F');
        $monthData = $monthlyFuelRaw->firstWhere('month', (float)$m);
        $chartData[] = $monthData ? round($monthData->total, 2) : 0;
    }

    // Data untuk Grafik Top 10 Highest Fuel Fills (Bulan Ini)
    $currentMonthName = Carbon::now()->translatedFormat('F Y');
    $topFuelFills = FuelFill::select('registration')
        ->selectRaw('SUM(fill_ammount_litres) as total')
        ->whereBetween('fill_timestamp', [$startOfMonth, $endOfMonth])
        ->groupBy('registration')
        ->orderByDesc('total')
        ->limit(10)
        ->get();

    $topFuelLabels = $topFuelFills->pluck('registration');
    $topFuelData = $topFuelFills->pluck('total');

    return view('dashboard', compact(
        'totalVehicles', 'totalFuelConsumed', 'totalFuelFills', 'totalDistance', 'totalDrivingHours', 'totalIdleHours',
        'chartLabels', 'chartData', 'topFuelLabels', 'topFuelData', 'currentMonthName'
    ));
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
    // Rute untuk data pengisian bahan bakar
    Route::get('/fuel-fill', [FuelFillController::class, 'index'])->name('fuel-fills.index');
    Route::post('/fuel-fill/sync', [FuelFillController::class, 'sync'])->name('fuel-fills.sync');
    // Rute untuk data perjalanan
    Route::get('/trips/sync', [TripSyncController::class, 'index'])->name('trips.sync.index');
    Route::post('/trips/sync', [TripSyncController::class, 'sync'])->name('trips.sync.process');
    // Rute untuk laporan aktivitas kendaraan
    Route::get('/reports/vehicle-activities', [ActivityReportController::class, 'index'])->name('reports.activity.index');
    Route::get('/reports/vehicle-activities/data', [ActivityReportController::class, 'getData'])->name('reports.activity.data');
    Route::get('/reports/vehicle-activities/export', [ActivityReportController::class, 'export'])->name('reports.activity.export');
});
// Admin routes for User Management (only admin role)
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserManagementController::class, 'create'])->name('users.create');
    Route::post('/users', [UserManagementController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [UserManagementController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserManagementController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');
});

require __DIR__.'/auth.php';
