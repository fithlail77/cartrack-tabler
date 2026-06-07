<?php

namespace App\Jobs;

use App\Services\CartrackTripService;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SyncTripsJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(CartrackTripService $cartrackTrips): void
    {
        // Format waktu sesuai kebutuhan Cartrack API (Y-m-d H:i:s) untuk H-1
        $start = Carbon::yesterday()->startOfDay()->format('Y-m-d H:i:s');
        $end = Carbon::yesterday()->endOfDay()->format('Y-m-d H:i:s');

        // Panggil Service untuk sinkronisasi
        $cartrackTrips->syncTripsData($start, $end);
    }
}
