<?php

namespace App\Services;

use App\Models\Vehicle;
use App\Models\FuelConsumedLevel;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CartrackFuelService
{
    protected string $apiUrl;
    protected string $apiUsername;
    protected string $apiPassword;

    public function __construct()
    {
        $this->apiUrl = config('services.cartrack.url', env('CARTRACK_API_URL'));
        $this->apiUsername = config('services.cartrack.username', env('CARTRACK_API_USERNAME'));
        $this->apiPassword = config('services.cartrack.password', env('CARTRACK_API_PASSWORD'));
    }

    public function syncFuelData(?string $startLocal = null, ?string $endLocal = null): array
    {
        try {
            // Cartrack API biasanya meminta format "Y-m-d H:i:s" untuk timestamp
            if ($startLocal && $endLocal) {
                $startTimestamp = Carbon::parse($startLocal, 'Asia/Jakarta')->setTimezone('UTC')->format('Y-m-d H:i:s');
                $endTimestamp = Carbon::parse($endLocal, 'Asia/Jakarta')->setTimezone('UTC')->format('Y-m-d H:i:s');
            } else {
                $endTimestamp = now()->utc()->format('Y-m-d H:i:s');
                $startTimestamp = now()->utc()->subHours(24)->format('Y-m-d H:i:s');
            }

            $vehicles = Vehicle::whereNotNull('registration')->get()->keyBy('registration');

            if ($vehicles->isEmpty()) {
                return ['status' => 'warning', 'message' => 'Tidak ada data kendaraan di database lokal.'];
            }

            // Normalisasi Base URL
            // Membuang string '/vehicles' jika terbawa dari konfigurasi di .env
            // Sehingga menjadi murni "https://fleetapi-id.cartrack.com/rest"
            $baseUrl = str_replace('/vehicles', '', $this->apiUrl);
            $baseUrl = rtrim($baseUrl, '/');

            $totalSynced = 0;

            // Menggunakan chunk yang lebih kecil (misal 30) agar beban request tidak terlalu berat dalam satu waktu
            foreach ($vehicles->chunk(50) as $chunkIndex => $chunk) {
                // Dokumentasi mewajibkan 'registrations' dikirim dalam bentuk Array
                $registrationsArray = $chunk->keys()->toArray();
                $currentPage = 1;
                $lastPage = 1;

                do {
                    // Menggunakan metode POST sesuai dokumentasi Cartrack
                    // Menambahkan .retry(3, 2000) -> Mencoba kembali 3x dengan jeda 2 detik jika gagal (termasuk error 429)
                    $response = Http::withBasicAuth($this->apiUsername, $this->apiPassword)
                                    ->withoutVerifying()
                                    ->retry(3, 2000)
                                    ->post($baseUrl . '/fuel/level?page=' . $currentPage, [
                                        'start_timestamp' => $startTimestamp,
                                        'end_timestamp'   => $endTimestamp,
                                        'registrations'   => $registrationsArray
                                    ]);

                    if ($response->failed()) {
                        $errorBody = $response->body();
                        Log::error("Cartrack Fuel Sync API Error pada Chunk " . ($chunkIndex + 1), [
                            'status' => $response->status(), 
                            'response' => $errorBody
                        ]);
                        // Gunakan break untuk keluar dari do-while, tapi lanjut ke chunk kendaraan berikutnya
                        break; 
                    }

                    $data = $response->json('data');
                    $lastPage = $response->json('meta.last_page') ?? 1;

                    if (!empty($data)) {
                        $fuelDataToUpsert = [];

                        foreach ($data as $item) {
                            $reg = $item['registration'] ?? null;
                            $localVehicle = $vehicles->get($reg);

                            if ($localVehicle) {
                                $startPeriod = $item['start_period'] ?? [];
                                $endPeriod = $item['end_period'] ?? [];

                                $fuelDataToUpsert[] = [
                                    'vehicle_id'             => $localVehicle->vehicle_id,
                                    'registration'           => $reg,
                                    'start_period_liters'    => $startPeriod['liters'] ?? 0,
                                    'start_period_timestamp' => isset($startPeriod['timestamp']) ? Carbon::parse($startPeriod['timestamp'])->timezone('Asia/Jakarta')->format('Y-m-d H:i:s') : null,
                                    'end_period_liters'      => $endPeriod['liters'] ?? 0,
                                    'end_period_timestamp'   => isset($endPeriod['timestamp']) ? Carbon::parse($endPeriod['timestamp'])->timezone('Asia/Jakarta')->format('Y-m-d H:i:s') : null,
                                    'estimated_fuel_used'    => $item['estimated_fuel_used'] ?? 0,
                                    'created_at'             => now(),
                                    'updated_at'             => now(),
                                ];
                            }
                        }

                        if (!empty($fuelDataToUpsert)) {
                            FuelConsumedLevel::upsert(
                                $fuelDataToUpsert,
                                ['vehicle_id', 'start_period_timestamp', 'end_period_timestamp'], 
                                ['start_period_liters', 'end_period_liters', 'estimated_fuel_used', 'updated_at'] 
                            );

                            $totalSynced += count($fuelDataToUpsert);
                        }
                    }

                    $currentPage++;
                    
                    // Tambahkan jeda 1 detik di setiap halaman untuk menghindari hit rate limit yang agresif
                    sleep(1);
                } while ($currentPage <= $lastPage);
            }

            if ($totalSynced === 0) {
                return ['status' => 'warning', 'message' => 'Tidak ada data baru yang ditarik. Periksa ketersediaan data di API untuk rentang waktu ini.'];
            }

            return ['status' => 'success', 'message' => "Proses sinkronisasi sukses. Total {$totalSynced} data berhasil ditarik."];

        } catch (\Exception $e) {
            Log::error('Cartrack Fuel Sync Exception: ' . $e->getMessage());
            return ['status' => 'error', 'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()];
        }
    }
}