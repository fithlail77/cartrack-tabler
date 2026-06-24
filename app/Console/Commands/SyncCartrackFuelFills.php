<?php

namespace App\Console\Commands;

use App\Jobs\FetchFuelFillsJob;
use Illuminate\Console\Command;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class SyncCartrackFuelFills extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cartrack:sync-fuel-fills {--start=} {--end=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Menarik data Fuel Fills dari Cartrack untuk seluruh kendaraan';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $startDateStr = $this->option('start') 
            ? Carbon::parse($this->option('start'))->format('Y-m-d') 
            : now()->subDay()->format('Y-m-d');
            
        $endDateStr = $this->option('end') 
            ? Carbon::parse($this->option('end'))->format('Y-m-d') 
            : now()->subDay()->format('Y-m-d');

        $this->info("Membuat antrean Fuel Fills dari {$startDateStr} sampai {$endDateStr}");

        $period = CarbonPeriod::create($startDateStr, $endDateStr);

        foreach ($period as $date) {
            $targetDate = $date->format('Y-m-d');
            
            FetchFuelFillsJob::dispatch($targetDate);
            
            $this->line("- Job Fuel Fills untuk tanggal {$targetDate} telah dimasukkan ke antrean.");
        }

        $this->info("Proses selesai. Silakan cek queue worker.");
    }
}
