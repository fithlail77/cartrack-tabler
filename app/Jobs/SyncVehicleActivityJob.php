<?php

namespace App\Jobs;

use App\Services\CartrackActivityService;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SyncVehicleActivityJob implements ShouldQueue
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
    public function handle(CartrackActivityService $activityService): void
    {
        // Menarik data 1 hari sebelumnya (H-1) format Y-m-d
        $date = Carbon::yesterday()->toDateString();
        
        $activityService->syncActivityByDate($date);
    }
}
