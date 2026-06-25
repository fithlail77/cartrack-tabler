<?php

namespace App\Console\Commands;

use App\Jobs\FetchCartrackTripsJob;
use Illuminate\Console\Command;

class SyncCartrackTrips extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cartrack:sync-trips {--start=} {--end=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Menarik data Trips (Perjalanan) dari Cartrack secara massal';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Default menarik data H-1 (Kemarin)
        // Timestamp Postman menunjukkan parameter lengkap HH:mm:ss
        $start = $this->option('start') ?? now()->subDay()->startOfDay()->format('Y-m-d 00:00:01');
        $end = $this->option('end') ?? now()->subDay()->endOfDay()->format('Y-m-d 23:59:59');

        $this->info("Membuat antrean sinkronisasi Trips dari {$start} s/d {$end}");

        FetchCartrackTripsJob::dispatch($start, $end);

        $this->info("Proses Queue (Antrean) Trips telah dimulai. Sistem akan otomatis me-looping setiap halaman.");
    }
}
