<?php

namespace App\Jobs;

use App\Services\CartrackFuelService;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SyncFuelConsumedJob implements ShouldQueue
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
    public function handle(CartrackFuelService $fuelService): void
    {
        // Menarik data 1 hari sebelumnya (H-1) dari awal hari hingga akhir hari
        $startLocal = Carbon::yesterday()->startOfDay()->format('Y-m-d H:i:s');
        $endLocal = Carbon::yesterday()->endOfDay()->format('Y-m-d H:i:s');

        $fuelService->syncFuelData($startLocal, $endLocal);
    }
}
