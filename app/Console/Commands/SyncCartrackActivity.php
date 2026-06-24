<?php

namespace App\Console\Commands;

use App\Models\Vehicle;
use App\Jobs\FetchVehicleActivityJob;
use Illuminate\Console\Command;

class SyncCartrackActivity extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cartrack:sync-activity {--start=} {--end=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Menarik data Vehicle Activity dari Cartrack untuk seluruh kendaraan';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Ambil input tanggal, potong hanya bagian YYYY-MM-DD
        // Jika tidak diisi (jalan via scheduler otomatis), defaultnya H-1 (kemarin)
        $startDateStr = $this->option('start') ? \Carbon\Carbon::parse($this->option('start'))->format('Y-m-d') : now()->subDay()->format('Y-m-d');
        $endDateStr   = $this->option('end') ? \Carbon\Carbon::parse($this->option('end'))->format('Y-m-d') : now()->subDay()->format('Y-m-d');

        $this->info("Membuat antrean Activity dari tanggal {$startDateStr} sampai {$endDateStr}");

        // Buat rentang tanggal (Misal: 18 Mei sampai 20 Mei = 3 Hari)
        $period = \Carbon\CarbonPeriod::create($startDateStr, $endDateStr);

        foreach ($period as $date) {
            $targetDate = $date->format('Y-m-d');
            
            // Masukkan HANYA 1 Job per Tanggal
            // Pastikan class FetchVehicleActivityJob sudah di-import di atas (use App\Jobs\FetchVehicleActivityJob;)
            \App\Jobs\FetchVehicleActivityJob::dispatch($targetDate);
            
            $this->line("- Job untuk tanggal {$targetDate} telah dimasukkan ke antrean.");
        }

        $this->info("Proses selesai. Silakan cek queue worker.");
    }
}
