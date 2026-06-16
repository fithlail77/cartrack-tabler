<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\VehicleActivity;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Exports\VehicleActivityExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class ActivityReportController extends Controller
{
    public function index()
    {
        // Mengambil daftar unit untuk dropdown filter
        $vehicles = Vehicle::orderBy('registration', 'asc')->get();
        return view('reports.vehicle_activities', compact('vehicles'));
    }

    public function getData(Request $request)
    {
        $query = VehicleActivity::query();

        // Filter Range Tanggal (Wajib)
        //if ($request->start_date && $request->end_date) {
        //    $query->whereBetween('activity_date', [$request->start_date, $request->end_date]);
        // }
        if ($request->start_date && $request->end_date) {
            $query->whereDate('activity_date', '>=', $request->start_date)
                  ->whereDate('activity_date', '<=', $request->end_date);
        }

        // Filter Registrasi (Opsional)
        // Method when() hanya akan menjalankan query di dalamnya jika $request->registration memiliki nilai
        $query->when($request->registration, function ($q) use ($request) {
            return $q->where('registration', $request->registration);
        });

        return DataTables::of($query)
            ->addIndexColumn()
            // Tambahkan kolom ini agar DataTables tidak crash jika datanya kosong/tidak ada di DB
            ->addColumn('driving_time_seconds', function($row) {
                return $row->driving_time_seconds ?? 0;
            })
            ->addColumn('total_working_hours', function($row) {
                return $row->total_working_hours ?? '00:00:00';
            })
            ->addColumn('driver_id', function($row) {
                return $row->driver_id ?? '-';
            })
            ->make(true);
    }

    public function export(Request $request) 
    {
        $fileName = 'Report_Vehicle_Activity_' . now()->format('Ymd_His') . '.xlsx';
        return Excel::download(new VehicleActivityExport($request), $fileName);
    }
}