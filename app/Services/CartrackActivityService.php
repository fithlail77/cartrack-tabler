<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\VehicleActivity;

class CartrackActivityService
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
     * Mengambil data aktivitas kendaraan berdasarkan tanggal tertentu
     * * @param string $date Format: Y-m-d (Contoh: 2026-05-18)
     */
    public function syncActivityByDate(string $date)
    {
        try {
            // Melakukan request dengan query parameter filter[date]
            $response = Http::withBasicAuth($this->username, $this->password)
                            ->timeout(45)
                            ->withoutVerifying() // Bypass SSL jika di localhost
                            ->get("{$this->apiUrl}/vehicles/activity", [
                                'filter' => [
                                    'date' => $date
                                ]
                            ]);

            if ($response->successful()) {
                $responseData = $response->json();
                $activities = $responseData['data'] ?? [];

                foreach ($activities as $activity) {
                    // Karena 'drivers' berbentuk array di dalam objek, ambil driver pertama jika ada
                    $driverData = !empty($activity['drivers']) ? $activity['drivers'][0] : null;

                    // Menggunakan updateOrCreate dengan composite key (vehicle_id & activity_date)
                    VehicleActivity::updateOrCreate(
                        [
                            'vehicle_id'    => $activity['vehicle_id'],
                            'activity_date' => $date
                        ],
                        [
                            'registration'             => $activity['registration'] ?? null,
                            'chassis_number'           => $activity['chassis_number'] ?? null,
                            'first_ignition_on'        => $activity['first_ignition_on'] ?? null,
                            'last_ignition_off'        => $activity['last_ignition_off'] ?? null,
                            'idle_time_seconds'        => $activity['idle_time_seconds'] ?? 0,
                            'driving_time_seconds'     => $activity['driving_time_seconds'] ?? 0,
                            'total_working_hours'      => $activity['total_working_hours'] ?? null,
                            'total_break_hours'        => $activity['total_break_hours'] ?? null,
                            'total_break_time_trimmed' => $activity['total_break_time_trimmed'] ?? null,
                            
                            // Mapping data driver hasil ekstraksi array
                            'driver_id'                => $driverData['driver_id'] ?? null,
                            'first_name'               => $driverData['first_name'] ?? null,
                            'last_name'                => $driverData['last_name'] ?? null,
                        ]
                    );
                }

                Log::info("Cartrack Activity Sync Sukses untuk tanggal: {$date}");
                return [
                    'status' => true,
                    'message' => 'Berhasil menyinkronkan ' . count($activities) . ' data aktivitas kendaraan.'
                ];
            }

            Log::error("Cartrack API Gagal. Status: " . $response->status());
            return [
                'status' => false,
                'message' => 'Gagal merespons dari API Cartrack. Status: ' . $response->status()
            ];

        } catch (\Exception $e) {
            Log::error("Exception pada CartrackActivityService: " . $e->getMessage());
            return [
                'status' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ];
        }
    }
}