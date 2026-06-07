<?php

namespace App\Services;

use App\Models\CartrackTrip;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CartrackTripService
{
    protected $apiUrl;
    protected $username;
    protected $password;

    public function __construct()
    {
        $this->apiUrl = env('CARTRACK_API_URL');
        $this->username = env('CARTRACK_API_USERNAME');
        $this->password = env('CARTRACK_API_PASSWORD');
    }

    /**
     * Mengambil dan sinkronisasi data dari API
     */
    public function syncTripsData($startTimestamp, $endTimestamp)
    {
        // Mencegah timeout saat penarikan data yang memakan waktu lama (seperti rentang waktu 31 hari)
        set_time_limit(0);

        $page = 1;
        $lastPage = 1;
        $totalSynced = 0;

        try {
            // Loop untuk menangani pagination dari Cartrack API
            do {
                $response = Http::withBasicAuth($this->username, $this->password)
                    ->withoutVerifying()
                    ->timeout(300) // Menambahkan timeout 5 menit untuk query besar
                    ->retry(5, 5000) // Mengulangi request 5x dengan jeda 5 detik jika gagal/timeout/DNS error
                    ->get("{$this->apiUrl}/trips", [
                        'start_timestamp' => $startTimestamp,
                        'end_timestamp' => $endTimestamp,
                        'page' => $page
                    ]);

                if ($response->successful()) {
                    $data = $response->json();
                    
                    // Simpan data ke database
                    foreach ($data['data'] as $trip) {
                        $this->saveTripToDatabase($trip);
                        $totalSynced++;
                    }

                    // Update informasi halaman berdasarkan response meta
                    if (isset($data['meta']['last_page'])) {
                        $lastPage = $data['meta']['last_page'];
                    }
                    
                    $page++;
                    
                    // Jeda 6 detik setiap selesai 1 halaman (sama seperti Fuel Service) 
                    // agar IP tidak diblokir sepenuhnya oleh sistem proteksi Cartrack
                    sleep(6);
                } else {
                    Log::error("Cartrack API Error pada page {$page}: " . $response->body());
                    throw new \Exception("Gagal mengambil data dari API Cartrack pada halaman {$page}.");
                }

            } while ($page <= $lastPage);

            return [
                'status' => 'success',
                'message' => "Berhasil sinkronisasi {$totalSynced} data trip."
            ];

        } catch (\Exception $e) {
            Log::error("Sync Error: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Helper untuk insert/update data untuk mencegah duplikasi (Clean Code)
     */
    private function saveTripToDatabase(array $tripData)
    {
        // updateOrCreate mengecek apakah trip_id sudah ada, jika ada di-update, jika tidak di-insert.
        CartrackTrip::updateOrCreate(
            ['trip_id' => $tripData['trip_id']], // Kondisi unik
            [
                'vehicle_id' => $tripData['vehicle_id'] ?? null,
                'registration' => $tripData['registration'] ?? null,
                'start_timestamp' => $tripData['start_timestamp'] ?? null,
                'end_timestamp' => $tripData['end_timestamp'] ?? null,
                'trip_duration' => $tripData['trip_duration'] ?? null,
                'trip_duration_seconds' => $tripData['trip_duration_seconds'] ?? null,
                'idle_time' => $tripData['idle_time'] ?? null,
                'idle_time_seconds' => $tripData['idle_time_seconds'] ?? null,
                'clock_start' => $tripData['clock_start'] ?? null,
                'clock_end' => $tripData['clock_end'] ?? null,
                'start_location' => $tripData['start_location'] ?? null,
                'end_location' => $tripData['end_location'] ?? null,
                'start_odometer' => $tripData['start_odometer'] ?? null,
                'end_odometer' => $tripData['end_odometer'] ?? null,
                'trip_distance' => $tripData['trip_distance'] ?? null,
                'start_geofence_name' => $tripData['start_geofence_name'] ?? null,
                'end_geofence_name' => $tripData['end_geofence_name'] ?? null,
                'max_speed' => $tripData['max_speed'] ?? null,
                'driver_id' => $tripData['driver_id'] ?? null,
                'driver_name' => $tripData['driver_name'] ?? null,
                'driver_surname' => $tripData['driver_surname'] ?? null,
                'start_coordinates' => $tripData['start_coordinates'] ?? null,
                'end_coordinates' => $tripData['end_coordinates'] ?? null,
            ]
        );
    }
}