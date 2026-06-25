<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\FuelLevel;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Exports\FuelConsumedExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class FuelReportController extends Controller
{
    public function index()
    {
        // Mengambil daftar unit untuk dropdown filter
        $vehicles = Vehicle::orderBy('registration', 'asc')->get();
        return view('reports.fuel_consumed', compact('vehicles'));
    }

    public function getData(Request $request)
    {
        $query = FuelLevel::query();

        // Filter Range Tanggal berdasarkan start_period_timestamp
        if ($request->start_date && $request->end_date) {
            $query->whereDate('start_timestamp', '>=', $request->start_date)
                  ->whereDate('start_timestamp', '<=', $request->end_date);
        }

        // Filter Registrasi (Opsional)
        $query->when($request->registration, function ($q) use ($request) {
            return $q->where('registration', $request->registration);
        });

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('start_timestamp', function($row) {
                return $row->start_timestamp ? Carbon::parse($row->start_timestamp)->format('d-m-Y H:i:s') : '-';
            })
            ->editColumn('end_timestamp', function($row) {
                return $row->end_timestamp ? Carbon::parse($row->end_timestamp)->format('d-m-Y H:i:s') : '-';
            })
            ->editColumn('start_liters', function($row) {
                return number_format($row->start_liters, 2) . ' L';
            })
            ->editColumn('end_liters', function($row) {
                return number_format($row->end_liters, 2) . ' L';
            })
            ->editColumn('estimated_fuel_used', function($row) {
                return number_format($row->estimated_fuel_used, 2) . ' L';
            })
            ->make(true);
    }

    public function export(Request $request) 
    {
        $fileName = 'Report_Fuel_Consumed_' . now()->format('Ymd_His') . '.xlsx';
        return Excel::download(new FuelConsumedExport($request), $fileName);
    }
}