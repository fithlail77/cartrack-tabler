<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FuelLevel;
use App\Services\CartrackFuelService;
use Carbon\Carbon;

class FuelConsumedController extends Controller
{
    public function index(Request $request)
    {
        $query = FuelLevel::query();

        // Menggunakan H-1 (Kemarin) sebagai default jika tidak ada filter yang dipilih
        $startDate = $request->get('filter_start', Carbon::yesterday()->toDateString());
        $endDate = $request->get('filter_end', Carbon::yesterday()->toDateString());

        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        $query->whereBetween('end_timestamp', [$start, $end]);

        $fuels = $query->orderBy('end_timestamp', 'desc')->paginate(10);
        
        return view('cartrack.fuel_consumed', compact('fuels'));
    }

    public function syncApi(Request $request, CartrackFuelService $fuelService)
    {
        // Validasi input dari Form UI. 
        $request->validate([
            'start_timestamp' => 'required|date',
            'end_timestamp'   => 'required|date|after_or_equal:start_timestamp',
        ]);

        // Eksekusi penarikan dengan membawa rentang tanggal
        $result = $fuelService->syncFuelData(
            $request->start_timestamp,
            $request->end_timestamp
        );

        if ($result['status'] === 'success') {
            return redirect()->route('fuels.index')->with('success', $result['message']);
        }

        return redirect()->route('fuels.index')->with('error', $result['message']);
    }
}
