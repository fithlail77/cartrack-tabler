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
        try {
            $response = Http::withBasicAuth($this->username, $this->password)
                ->timeout(30)
                ->withoutVerifying()
                ->get("{$this->baseUrl}/fuel/fills/{$registration}", [
                    'start_timestamp' => $startTimestamp,
                    'end_timestamp'   => $endTimestamp,
                ]);

            if ($response->successful()) {
                // Mengambil isi dari key "data" sesuai dengan struktur JSON di Postman
                return $response->json('data') ?? [];
            }

            Log::error("Cartrack API Error untuk {$registration}: " . $response->body());
            return [];

        } catch (\Exception $e) {
            Log::error("Koneksi API Cartrack gagal untuk {$registration}: " . $e->getMessage());
            return [];
        }
    }
}