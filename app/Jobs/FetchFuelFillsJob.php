<?php

namespace App\Jobs;
use App\Models\Vehicle;
use App\Models\FuelFill;
use App\Services\CartrackFuelLevelService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class FetchFuelFillsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public string $targetDate)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(CartrackFuelLevelService $cartrackService): void
    {
        Log::info("Memulai sinkronisasi Fuel Fills untuk tanggal: {$this->targetDate}");

        // Format waktu seperti pada Postman: YYYY-MM-DD 00:00:01 hingga YYYY-MM-DD 23:59:00
        $startTimestamp = $this->targetDate . ' 00:00:01';
        $endTimestamp   = $this->targetDate . ' 23:59:59';

        // Panggil service yang sudah bisa menangani pagination
        $fillsData = $cartrackService->getFuelFills($startTimestamp, $endTimestamp);

        // Jika tidak ada data atau terjadi error di service, hentikan job
        if (empty($fillsData)) {
            Log::info("Cartrack Sync: Tidak ada data Fuel Fills untuk tanggal {$this->targetDate} atau terjadi error saat penarikan.");
            return;
        }

        $successCount = 0;
        // Ambil semua kendaraan dari DB lokal untuk dicocokkan, gunakan key 'registration' agar pencarian lebih cepat
        $localVehicles = Vehicle::whereNotNull('registration')->get()->keyBy('registration');

        foreach ($fillsData as $data) {
            $registration = $data['registration'] ?? null;

            // Cari kendaraan di DB lokal berdasarkan plat nomor dari API
            $vehicle = $localVehicles->get($registration);

            // Jika plat nomor dari API tidak ada di DB lokal, atau tidak ada timestamp, lewati data ini
            if (!$vehicle || empty($data['fill_timestamp'])) {
                continue;
            }

            $fillTimestamp = Carbon::parse($data['fill_timestamp']);

            FuelFill::updateOrCreate(
                [
                    // Parameter Kunci untuk updateOrCreate
                    'vehicle_id'     => $vehicle->id,
                    'fill_timestamp' => $fillTimestamp,
                ],
                [
                    'registration'        => $registration,
                    'fill_ammount_litres' => $data['fill_amount_litres'] ?? 0,
                    'fill_odometer'       => $data['fill_odometer'] ?? null,
                    'fill_location'       => $data['fill_location'] ?? null,
                    'latitude'            => $data['latitude'] ?? null,
                    'longitude'           => $data['longitude'] ?? null,
                    'accurate'            => $data['accurate'] ?? false,
                ]
            );

            $successCount++;
        }

        Log::info("Sukses sync Fuel Fills massal tanggal {$this->targetDate}. Total record tersimpan/diperbarui: {$successCount}");
    }

    public function failed(Throwable $exception): void
    {
        Log::error("Fuel Fills Job Gagal untuk tanggal {$this->targetDate} - {$exception->getMessage()}");
    }
}
