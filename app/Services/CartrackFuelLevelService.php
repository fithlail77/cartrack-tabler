<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\PendingRequest;
use Exception;
use Illuminate\Support\Facades\Log;

class CartrackFuelLevelService
{
    /**
     * Konfigurasi dasar untuk HTTP Client Laravel menggunakan Basic Auth
     */
    protected function client(): PendingRequest
    {
        return Http::baseUrl(config('services.cartrack.url'))
            // Menggunakan Basic Authentication (Username & Password)
            ->withBasicAuth(
                config('services.cartrack.username'), 
                config('services.cartrack.password')
            )
            ->withoutVerifying()
            ->timeout(10)
            ->retry(3, 1000); // Otomatis retry 3 kali jika gagal
    }

    /**
     * Mengambil data level bahan bakar berdasarkan plat/registrasi
     */
    public function getFuelLevel(string $registration, string $startTimestamp, string $endTimestamp): ?array
    {
        try {
            $response = $this->client()->get("/fuel/level/{$registration}", [
                'start_timestamp' => $startTimestamp,
                'end_timestamp' => $endTimestamp,
            ]);

            // Jika status code 200 OK
            if ($response->successful()) {
                return $response->json('data'); 
            }
            
            // Opsional: Anda bisa menambahkan log khusus jika response gagal (misal 401 Unauthorized)
            Log::error('Cartrack API Error: ' . $response->status() . ' - ' . $response->body());
            
            return null;
        } catch (Exception $e) {
            report($e);
            return null;
        }
    }

    public function getVehicleActivity(string $date): ?array
    {
        try {
            // Catatan: Sesuaikan endpoint URL `/rest/activity/` dengan dokumentasi resmi Cartrack jika berbeda
            $response = $this->client()->get("/vehicles/activity", [
                'filter[date]' => $date,
            ]);

            if ($response->successful()) {
                return $response->json('data'); // Mengambil array dari key 'data'
            }

            // Opsional: Anda bisa menambahkan log khusus jika response gagal (misal 401 Unauthorized)
            Log::error('Cartrack API Error: ' . $response->status() . ' - ' . $response->body());
            
            return null;
        } catch (Exception $e) {
            report($e);
            return null;
        }
    }

    public function getFuelFills(string $startTimestamp, string $endTimestamp): ?array
    {
        $allData = [];
        $currentPage = 1;
        $lastPage = 1;

        try {
            do {
                $response = $this->client()->get("/fuel/fills", [
                    'start_timestamp' => $startTimestamp,
                    'end_timestamp'   => $endTimestamp,
                    'page'            => $currentPage, // Tambahkan parameter 'page'
                ]);

                if ($response->failed()) {
                    // Jika gagal di halaman manapun, catat error dan hentikan proses
                    Log::error("Cartrack Fuel Fills Error di halaman {$currentPage}: " . $response->body());
                    // Kembalikan null jika terjadi error, atau data yang sudah terkumpul jika diinginkan
                    return null;
                }

                $pageData = $response->json('data');

                if (!empty($pageData)) {
                    // Gabungkan data dari halaman ini ke array utama
                    $allData = array_merge($allData, $pageData);
                }

                // Update info halaman terakhir dari response API
                $lastPage = $response->json('meta.last_page') ?? 1;

                // Lanjut ke halaman berikutnya
                $currentPage++;

            } while ($currentPage <= $lastPage);

            // Kembalikan semua data yang sudah terkumpul
            return $allData;

        } catch (\Exception $e) {
            Log::error("Koneksi API Cartrack Fuel Fills gagal: " . $e->getMessage());
            report($e);
            return null;
        }
    }

    /**
     * Mengambil data Trips secara massal (berisi Pagination)
     * Mengembalikan struktur lengkap agar Job bisa membaca 'meta' untuk melihat total page
     */
    public function getTrips(string $startTimestamp, string $endTimestamp, int $page = 1): ?array
    {
        try {
            // Catatan: Sesuaikan "/trips" dengan endpoint asli Cartrack jika dibutuhkan (misal "/rest/trips")
            $response = $this->client()->get("/trips", [
                'start_timestamp' => $startTimestamp,
                'end_timestamp' => $endTimestamp,
                'page' => $page // Parameter pagination API Cartrack
            ]);

            if ($response->successful()) {
                // Return full JSON termasuk 'meta' dan 'data' agar Job bisa baca $response['meta']['last_page']
                return $response->json(); 
            }

            Log::error("Cartrack API Error (Trips Page {$page}): " . $response->status() . " - " . $response->body());
            return null;
        } catch (Exception $e) {
            report($e);
            return null;
        }
    }
}