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
}