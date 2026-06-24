<?php

namespace App\Jobs;

use App\Models\Vehicle;
use App\Models\VehicleActivity;
use App\Services\CartrackFuelLevelService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class FetchVehicleActivityJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public string $targetDate)
    {
        // Parameter diubah menjadi string targetDate agar sesuai dengan Command
    }

    /**
     * Execute the job.
     */
    public function handle(CartrackFuelLevelService $cartrackService): void
    {
        Log::info("Memulai sinkronisasi massal Activity untuk tanggal: {$this->targetDate}");

        // Memanggil service dengan parameter $this->targetDate yang sekarang valid
        $dataArray = $cartrackService->getVehicleActivity($this->targetDate);

        // Validasi jika API tidak mengembalikan data / error
        if (empty($dataArray) || !is_array($dataArray)) {
            Log::info("Cartrack Sync: Tidak ada data activity untuk tanggal {$this->targetDate}");
            return;
        }

        $successCount = 0;

        // Loop data massal hasil balikan dari Cartrack
        foreach ($dataArray as $data) {
            
            $registration = $data['registration'] ?? null;
            if (empty($registration)) {
                continue; // Lewati jika tidak ada plat nomor dari API
            }

            // Cari ID Kendaraan di DB lokal berdasarkan plat nomor ('registration')
            $vehicleLocal = Vehicle::where('registration', $registration)->first();

            // Jika plat nomor dari API tidak terdaftar di DB lokal kita, lewati
            if (!$vehicleLocal) {
                continue;
            }

            // Ekstrak data Driver (ambil driver pertama jika ada)
            $driverId = null;
            $firstName = null;
            $lastName = null;

            if (!empty($data['drivers']) && is_array($data['drivers']) && count($data['drivers']) > 0) {
                $driverId = $data['drivers'][0]['driver_id'] ?? null;
                $firstName = $data['drivers'][0]['first_name'] ?? null;
                $lastName = $data['drivers'][0]['last_name'] ?? null;
            }

            VehicleActivity::updateOrCreate(
                [
                    // Parameter Kunci: 1 Unit = 1 Laporan Activity per Hari
                    'vehicle_id'  => $vehicleLocal->id,
                    'activity_date' => $this->targetDate,
                ],
                [
                    'registration'             => $registration,
                    'chassis_number'           => $data['chassis_number'] ?? null,
                    'first_ignition_on'        => !empty($data['first_ignition_on']) ? Carbon::parse($data['first_ignition_on']) : null,
                    'last_ignition_off'        => !empty($data['last_ignition_off']) ? Carbon::parse($data['last_ignition_off']) : null,
                    'idle_time_seconds'        => $data['idle_time_seconds'] ?? 0,
                    'driving_time_seconds'     => $data['driving_time_seconds'] ?? 0,
                    'total_working_hours'      => $data['total_working_hours'] ?? null,
                    'total_break_hours'        => $data['total_break_hours'] ?? null,
                    'total_break_time_trimmed' => $data['total_break_time_trimmed'] ?? null,
                    // Masukkan data driver yang sudah diekstrak
                    'driver_id'                => $driverId,
                    'first_name'               => $firstName,
                    'last_name'                => $lastName,
                ]
            );

            $successCount++;
        }

        Log::info("Sukses sync Activity massal tanggal {$this->targetDate}. Total unit tersimpan: {$successCount}");
    }

    public function failed(Throwable $exception): void
    {
        Log::error("Activity Job Gagal Total untuk tanggal {$this->targetDate} - {$exception->getMessage()}");
    }
}

