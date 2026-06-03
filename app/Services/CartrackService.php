<?php

namespace App\Services;

use App\Models\Vehicle;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CartrackService
{
    protected $apiUrl;
    protected $apiUsername;
    protected $apiPassword;

    public function __construct()
    {
        // Mengambil kredensial dari environment variable
        // Best practice: Daftarkan key ini di dalam file config/services.php
        $this->apiUrl = env('CARTRACK_API_URL');
        $this->apiUsername = env('CARTRACK_API_USERNAME');
        $this->apiPassword = env('CARTRACK_API_PASSWORD');
    }

    public function syncVehicles(): array
    {
        try {
            $currentPage = 1;
            $lastPage = 1; // Default awal, akan di-update dari response API
            $totalSynced = 0;

            // Mulai proses perulangan untuk mengambil semua halaman API
            do {
                // Tambahkan parameter 'page' di HTTP Client
                $response = Http::withBasicAuth($this->apiUsername, $this->apiPassword)
                                ->withoutVerifying() 
                                ->get($this->apiUrl . '/vehicles', [
                                    'page' => $currentPage
                                ]);

                if ($response->failed()) {
                    throw new \Exception('Gagal mengambil data dari API halaman ' . $currentPage . '. Status HTTP: ' . $response->status());
                }

                $data = $response->json('data');
                
                // Ambil info halaman terakhir dari response API
                // Jika tidak ada key meta.last_page, anggap hanya ada 1 halaman
                $lastPage = $response->json('meta.last_page') ?? 1;

                if (empty($data) && $currentPage === 1) {
                    return ['status' => 'warning', 'message' => 'Tidak ada data kendaraan yang ditemukan dari endpoint API.'];
                }

                if (!empty($data)) {
                    $vehiclesToUpsert = [];

                    foreach ($data as $item) {
                        $vehiclesToUpsert[] = [
                            'vehicle_id'     => $item['vehicle_id'],
                            'registration'   => $item['registration'],
                            'vehicle_name'   => $item['vehicle_name'],
                            'max_speed'      => $item['max_speed'],
                            'manufacturer'   => $item['manufacturer'],
                            'model'          => $item['model'],
                            'model_year'     => $item['model_year'],
                            'colour'         => $item['colour'],
                            'chassis_number' => $item['chassis_number'],
                            'vehicle_type'   => $item['vehicle_type'],
                            'created_at'     => now(),
                            'updated_at'     => now(),
                        ];
                    }

                    // Eksekusi Upsert per halaman.
                    // Melakukan update massal per 10 data lebih aman dan tidak membebani memori.
                    Vehicle::upsert(
                        $vehiclesToUpsert,
                        ['vehicle_id'], 
                        [
                            'registration', 'vehicle_name', 'max_speed', 'manufacturer', 
                            'model', 'model_year', 'colour', 'chassis_number', 'vehicle_type', 'updated_at'
                        ]
                    );

                    // Tambahkan jumlah data yang berhasil di-sync
                    $totalSynced += count($vehiclesToUpsert);
                }

                // Lanjut ke halaman berikutnya
                $currentPage++;

            } while ($currentPage <= $lastPage); // Loop akan berhenti jika halaman saat ini melebihi halaman terakhir

            return ['status' => 'success', 'message' => $totalSynced . ' data kendaraan (dari total ' . $lastPage . ' halaman) berhasil disinkronisasi.'];

        } catch (\Exception $e) {
            Log::error('Cartrack API Sync Error: ' . $e->getMessage());
            return ['status' => 'error', 'message' => 'Terjadi kesalahan saat memproses API: ' . $e->getMessage()];
        }
    }
}