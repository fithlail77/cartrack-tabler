<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CartrackTripService;

class TripSyncController extends Controller
{
    protected $cartrackTrips;

    // Inject Service menggunakan Dependency Injection
    public function __construct(CartrackTripService $cartrackTrips)
    {
        $this->cartrackTrips = $cartrackTrips;
    }

    public function index(\Illuminate\Http\Request $request)
    {
        $query = \App\Models\CartrackTrip::query();

        // Menggunakan hari ini sebagai default jika tidak ada filter yang dipilih
        $startDate = $request->get('filter_start', \Carbon\Carbon::today()->toDateString());
        $endDate = $request->get('filter_end', \Carbon\Carbon::today()->toDateString());

        $start = \Carbon\Carbon::parse($startDate)->startOfDay();
        $end = \Carbon\Carbon::parse($endDate)->endOfDay();

        $query->whereBetween('start_timestamp', [$start, $end]);

        $trips = $query->orderBy('start_timestamp', 'desc')->paginate(10);
        
        return view('cartrack.trips', compact('trips'));
    }

    public function sync(Request $request)
    {
        // Validasi input tanggal
        $request->validate([
            'start_timestamp' => 'required|date',
            'end_timestamp' => 'required|date|after_or_equal:start_timestamp',
        ]);

        // Mencegah PHP berhenti otomatis saat loading data yang besar (seperti 31 hari)
        set_time_limit(0);

        // Format waktu sesuai kebutuhan Cartrack API (Y-m-d H:i:s)
        $start = \Carbon\Carbon::parse($request->start_timestamp)->format('Y-m-d H:i:s');
        $end = \Carbon\Carbon::parse($request->end_timestamp)->format('Y-m-d H:i:s');

        // Panggil Service
        $result = $this->cartrackTrips->syncTripsData($start, $end);

        if ($result['status'] === 'success') {
            return back()->with('success', $result['message']);
        }

        return back()->with('error', $result['message'])->withInput();
    }
}
