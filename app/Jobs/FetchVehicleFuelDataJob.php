<?php

namespace App\Jobs;

use App\Models\Vehicle;
use App\Services\CartrackFuelLevelService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class FetchVehicleFuelDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public Vehicle $vehicle, public string $startTimestamp, public string $endTimestamp)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(CartrackFuelLevelService $cartrackfuellevelService): void
    {
        // Asumsi field plat nomor di tabel vehicles bernama 'registration_number'
        $registration = $this->vehicle->registration;

        if (empty($registration)) {
            Log::warning("Job dilewati: Data kendaraan dengan ID {$this->vehicle->id} tidak memiliki plat nomor.");
            return; 
        }

        // Tarik data dari API
        $data = $cartrackfuellevelService->getFuelLevel(
            $registration, 
            $this->startTimestamp, 
            $this->endTimestamp
        );

        // 🛑 PERBAIKAN VALIDASI 1: Jika API error atau respons data kosong secara keseluruhan
        if (empty($data)) {
            Log::info("Cartrack Sync: Unit {$registration} dilewati karena tidak ada respons data untuk periode ini.");
            return; 
        }

        // 🛑 PERBAIKAN VALIDASI 2: Cek apakah data fuel consumed tidak ada, bernilai null, atau bernilai 0
        // Serta pastikan timestamp periodenya ada sebelum diproses oleh Carbon
        if (!isset($data['estimated_fuel_used']) || 
            $data['estimated_fuel_used'] === null || 
            empty($data['start_period']['timestamp']) || 
            empty($data['end_period']['timestamp'])) {
            
            Log::info("Cartrack Sync: Unit {$registration} dilewati karena tidak ada konsumsi BBM (fuel consumed) atau data periode tidak lengkap.");
            return; // Berhenti di sini, jangan simpan ke database
        }

        // Jika lolos semua validasi di atas, barulah data disimpan ke PostgreSQL
        \App\Models\FuelLevel::updateOrCreate(
            [
                'vehicle_id' => $this->vehicle->id,
                'start_timestamp' => \Carbon\Carbon::parse($data['start_period']['timestamp']),
            ],
            [
                'registration' => $registration,
                'start_liters' => $data['start_period']['liters'] ?? null,
                'end_liters' => $data['end_period']['liters'] ?? null,
                'end_timestamp' => \Carbon\Carbon::parse($data['end_period']['timestamp']),
                'estimated_fuel_used' => $data['estimated_fuel_used'],
                'calibrated' => $data['calibrated'] ?? false,
            ]
        );

        Log::info("Sukses sinkronisasi BBM untuk unit: {$registration}");
    }
}
