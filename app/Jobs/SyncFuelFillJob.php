<?php

namespace App\Jobs;

use App\Models\Vehicle;
use App\Models\FuelFill;
use App\Services\CartrackApiService;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SyncFuelFillJob implements ShouldQueue
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
    public function handle(CartrackApiService $cartrackService): void
    {
        // Format tanggal sesuai kebutuhan parameter Cartrack (YYYY-MM-DD HH:MM:SS) untuk H-1
        $startTimestamp = Carbon::yesterday()->startOfDay()->addSecond()->format('Y-m-d H:i:s'); // 00:00:01
        $endTimestamp = Carbon::yesterday()->endOfDay()->format('Y-m-d H:i:s'); // 23:59:59

        $vehicles = Vehicle::all();

        foreach ($vehicles as $vehicle) {
            $fillsData = $cartrackService->getFuelFills(
                $vehicle->registration, 
                $startTimestamp, 
                $endTimestamp
            );

            if (!empty($fillsData)) {
                foreach ($fillsData as $data) {
                    FuelFill::updateOrCreate(
                        [
                            'registration' => $data['registration'] ?? $vehicle->registration,
                            'fill_timestamp' => Carbon::parse($data['fill_timestamp'])->format('Y-m-d H:i:s'), 
                        ],
                        [
                            'vehicle_id' => $vehicle->id,
                            'fill_ammount_litres' => $data['fill_amount_litres'] ?? null, 
                            'fill_odometer' => $data['fill_odometer'] ?? null,
                            'fill_location' => $data['fill_location'] ?? null,
                            'latitude' => $data['latitude'] ?? null,
                            'longitude' => $data['longitude'] ?? null,
                            'accurate' => $data['accurate'] ?? false,
                        ]
                    );
                }
            }
        }
    }
}
