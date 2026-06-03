<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Services\CartrackService;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    // Method untuk menampilkan halaman
    public function index()
    {
        $vehicles = Vehicle::orderBy('updated_at', 'desc')->paginate(10);
        return view('cartrack.vehicles', compact('vehicles'));
    }

    // Method untuk mengeksekusi sinkronisasi API menggunakan Service Injection
    public function syncApi(CartrackService $cartrackService)
    {
        $result = $cartrackService->syncVehicles();

        if ($result['status'] === 'success') {
            return redirect()->route('vehicles.index')->with('success', $result['message']);
        }

        return redirect()->route('vehicles.index')->with('error', $result['message']);
    }
}