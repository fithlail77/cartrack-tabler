<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\CartrackTrip;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Exports\TripExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class TripReportController extends Controller
{
    public function index()
    {
        $vehicles = Vehicle::orderBy('registration', 'asc')->get();
        return view('reports.trips', compact('vehicles'));
    }

    public function getData(Request $request)
    {
        $query = CartrackTrip::query();

        if ($request->start_date && $request->end_date) {
            $query->whereDate('start_timestamp', '>=', $request->start_date)
                  ->whereDate('start_timestamp', '<=', $request->end_date);
        }

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
            ->editColumn('trip_distance', function($row) {
                return number_format(($row->trip_distance ?? 0) / 1000, 2) . ' km';
            })
            ->editColumn('driver_name', function($row) {
                $name = trim(($row->driver_name ?? '') . ' ' . ($row->driver_surname ?? ''));
                return $name ?: '-';
            })
            ->make(true);
    }

    public function export(Request $request)
    {
        $fileName = 'Report_Vehicle_Trips_' . now()->format('Ymd_His') . '.xlsx';
        return Excel::download(new TripExport($request), $fileName);
    }
}