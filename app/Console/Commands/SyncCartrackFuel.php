<?php

namespace App\Console\Commands;

use App\Models\Vehicle;
use App\Jobs\FetchVehicleFuelDataJob;
use Illuminate\Console\Command;

class SyncCartrackFuel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    //protected $signature = 'app:sync-cartrack-fuel';

    /**
     * The console command description.
     *
     * @var string
     */
    //protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Ubah bagian ini agar defaultnya adalah H-1 (Kemarin)
        $start = $this->option('start') ?? now()->subDay()->startOfDay()->format('Y-m-d H:i:s');
        $end = $this->option('end') ?? now()->subDay()->endOfDay()->format('Y-m-d H:i:s');

        $this->info("Memulai sinkronisasi data dari {$start} sampai {$end}");

        Vehicle::chunk(50, function ($vehicles) use ($start, $end) {
            foreach ($vehicles as $vehicle) {
                \App\Jobs\FetchVehicleFuelDataJob::dispatch($vehicle, $start, $end);
            }
        });

        $this->info("185 Unit kendaraan telah dimasukkan ke dalam antrian.");
    }
}
