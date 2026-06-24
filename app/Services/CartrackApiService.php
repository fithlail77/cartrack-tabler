<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CartrackApiService
{
    protected string $baseUrl;
    protected string $username;
    protected string $password;

    public function __construct()
    {
        $this->baseUrl = config('services.cartrack.url');
        $this->username = config('services.cartrack.username');
        $this->password = config('services.cartrack.password');
    }

    /**
     * Menarik data fuel fills dengan rentang waktu
     */
    public function getFuelFills(string $registration, string $startTimestamp, string $endTimestamp): array
    {
        $allData = [];
        $currentPage = 1;
        $lastPage = 1;

        try {
            do {
                $response = Http::withBasicAuth($this->username, $this->password)
                    ->timeout(30)
                    ->withoutVerifying()
                    ->get("{$this->baseUrl}/fuel/fills/{$registration}", [
                        'start_timestamp' => $startTimestamp,
                        'end_timestamp'   => $endTimestamp,
                        'page'            => $currentPage, // Tambahkan parameter 'page'
                    ]);

                if ($response->failed()) {
                    // Jika gagal di halaman manapun, catat error dan hentikan proses untuk kendaraan ini
                    Log::error("Cartrack API Error untuk {$registration} di halaman {$currentPage}: " . $response->body());
                    return []; // Kembalikan array kosong jika terjadi error
                }

                // Ambil data dari halaman saat ini
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
            Log::error("Koneksi API Cartrack gagal untuk {$registration}: " . $e->getMessage());
            return [];
        }
    }
}