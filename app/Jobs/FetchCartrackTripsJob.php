<?php

namespace App\Jobs;

use App\Models\CartrackTrip;
use App\Models\Vehicle;
use App\Services\CartrackFuelLevelService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class FetchCartrackTripsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // Batas waktu eksekusi Job: 1 Jam (karena pagination data trip bisa ribuan)
    public $timeout = 3600;

    /**
     * Create a new job instance.
     */
    public function __construct(public string $startTimestamp,
        public string $endTimestamp)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(CartrackFuelLevelService $cartrackService): void
    {
        Log::info("Memulai sinkronisasi Trips dari {$this->startTimestamp} s/d {$this->endTimestamp}");

        $page = 1;
        $lastPage = 1;
        $totalSaved = 0;

        do {
            $response = $cartrackService->getTrips($this->startTimestamp, $this->endTimestamp, $page);

            // Jika API error atau data kosong, hentikan loop
            if (empty($response) || empty($response['data'])) {
                Log::warning("Trips Sync: Data kosong pada page {$page} atau terjadi error API.");
                break;
            }

            // Loop array 'data'
            foreach ($response['data'] as $tripData) {
                // Opsional: Coba relasikan vehicle_id di tabel vehicle lokal
                $vehicle = Vehicle::where('registration', $tripData['registration'] ?? '')->first();
                $localVehicleId = $vehicle ? $vehicle->id : null;

                // Gunakan trip_id sebagai Unique Key pencegah duplikat
                CartrackTrip::updateOrCreate(
                    [
                        'trip_id' => $tripData['trip_id'],
                    ],
                    [
                        'vehicle_id'              => $localVehicleId ?? $tripData['vehicle_id'], // Gunakan lokal jika ada, fallback id cartrack
                        'registration'            => $tripData['registration'] ?? null,
                        'start_timestamp'         => !empty($tripData['start_timestamp']) ? Carbon::parse($tripData['start_timestamp']) : null,
                        'end_timestamp'           => !empty($tripData['end_timestamp']) ? Carbon::parse($tripData['end_timestamp']) : null,
                        'trip_duration'           => $tripData['trip_duration'] ?? null,
                        'trip_duration_seconds'   => $tripData['trip_duration_seconds'] ?? 0,
                        'idle_time'               => $tripData['idle_time'] ?? null,
                        'idle_time_seconds'       => $tripData['idle_time_seconds'] ?? 0,
                        'clock_start'             => $tripData['clock_start'] ?? null,
                        'clock_end'               => $tripData['clock_end'] ?? null,
                        'start_location'          => $tripData['start_location'] ?? null,
                        'end_location'            => $tripData['end_location'] ?? null,
                        'start_odometer'          => $tripData['start_odometer'] ?? null,
                        'end_odometer'            => $tripData['end_odometer'] ?? null,
                        'trip_distance'           => $tripData['trip_distance'] ?? 0,
                        'start_geofence_name'     => $tripData['start_geofence_name'] ?? null,
                        'end_geofence_name'       => $tripData['end_geofence_name'] ?? null,
                        'max_speed'               => $tripData['max_speed'] ?? 0,
                        'driver_id'               => $tripData['driver_id'] ?? null,
                        'driver_name'             => $tripData['driver_name'] ?? null,
                        'driver_surname'          => $tripData['driver_surname'] ?? null,
                        'start_coordinates'       => $tripData['start_coordinates'] ?? null, // Casted to JSON otomatis
                        'end_coordinates'         => $tripData['end_coordinates'] ?? null,
                    ]
                );
                
                $totalSaved++;
            }

            // Update variabel lastPage dari object 'meta'
            $lastPage = $response['meta']['last_page'] ?? 1;
            
            // Jeda 6 detik untuk mematuhi rate-limit Cartrack.
            // Dijalankan hanya jika bukan halaman terakhir, agar tidak membuang waktu 6 detik di akhir proses.
            if ($page < $lastPage) {
                Log::info("Trips Sync: Menunggu 6 detik sebelum menarik page " . ($page + 1));
                sleep(6); 
            }

            $page++;

        } while ($page <= $lastPage);

        Log::info("Selesai sinkronisasi Trips. Total data tersimpan: {$totalSaved}");
    }

    public function failed(Throwable $exception): void
    {
        Log::error("Trips Job Gagal Total: {$exception->getMessage()}");
    }
}
