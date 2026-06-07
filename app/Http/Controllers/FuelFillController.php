<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehicle;
use App\Models\FuelFill;
use App\Services\CartrackApiService;
use Carbon\Carbon;

class FuelFillController extends Controller
{
    protected CartrackApiService $cartrackService;

    public function __construct(CartrackApiService $cartrackService)
    {
        $this->cartrackService = $cartrackService;
    }

    public function index(Request $request)
    {
        $query = FuelFill::query();

        // Menggunakan H-1 (Kemarin) sebagai default jika tidak ada filter yang dipilih
        $startDate = $request->get('filter_start', Carbon::yesterday()->toDateString());
        $endDate = $request->get('filter_end', Carbon::yesterday()->toDateString());

        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        $query->whereBetween('fill_timestamp', [$start, $end]);

        $fuelFills = $query->orderBy('fill_timestamp', 'desc')->paginate(10);
        $vehicleCount = Vehicle::count();
        
        return view('cartrack.fillfuel', compact('fuelFills', 'vehicleCount'));
    }

    public function sync(Request $request)
    {
        // Validasi input tanggal dari form
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        set_time_limit(0); // Mencegah timeout saat loop 185 kendaraan

        // Format tanggal sesuai kebutuhan parameter Cartrack (YYYY-MM-DD HH:MM:SS)
        $startTimestamp = Carbon::parse($request->start_date)->startOfDay()->addSecond()->format('Y-m-d H:i:s'); // 00:00:01
        $endTimestamp = Carbon::parse($request->end_date)->endOfDay()->format('Y-m-d H:i:s'); // 23:59:59

        $vehicles = Vehicle::all();
        $totalSynced = 0;

        foreach ($vehicles as $vehicle) {
            $fillsData = $this->cartrackService->getFuelFills(
                $vehicle->registration, 
                $startTimestamp, 
                $endTimestamp
            );

            if (!empty($fillsData)) {
                foreach ($fillsData as $data) {
                    FuelFill::updateOrCreate(
                        [
                            'registration' => $data['registration'] ?? $vehicle->registration,
                            // Kita parse timestamp agar aman masuk ke PostgreSQL (meskipun ada format +07)
                            'fill_timestamp' => Carbon::parse($data['fill_timestamp'])->format('Y-m-d H:i:s'), 
                        ],
                        [
                            'vehicle_id' => $vehicle->id,
                            // Pastikan nama kolom di DB (fill_ammount_litres) dipasangkan dengan response API (fill_amount_litres)
                            'fill_ammount_litres' => $data['fill_amount_litres'] ?? null, 
                            'fill_odometer' => $data['fill_odometer'] ?? null,
                            'fill_location' => $data['fill_location'] ?? null,
                            'latitude' => $data['latitude'] ?? null,
                            'longitude' => $data['longitude'] ?? null,
                            'accurate' => $data['accurate'] ?? false,
                        ]
                    );
                    $totalSynced++;
                }
            }
        }

        return redirect()->back()->with('success', "Sinkronisasi rentang {$request->start_date} s/d {$request->end_date} selesai. Memperbarui $totalSynced data pengisian.");
    }
}
