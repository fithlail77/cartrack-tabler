<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\FuelFill;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Exports\FuelFillExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class FuelFillReportController extends Controller
{
    public function index()
    {
        $vehicles = Vehicle::orderBy('registration', 'asc')->get();
        return view('reports.fuel_fill', compact('vehicles'));
    }

    public function getData(Request $request)
    {
        $query = FuelFill::query();

        if ($request->start_date && $request->end_date) {
            $query->whereDate('fill_timestamp', '>=', $request->start_date)
                  ->whereDate('fill_timestamp', '<=', $request->end_date);
        }

        $query->when($request->registration, function ($q) use ($request) {
            return $q->where('registration', $request->registration);
        });

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('fill_ammount_litres', function($row) {
                return number_format($row->fill_ammount_litres, 2) . ' L';
            })
            ->editColumn('fill_odometer', function($row) {
                return number_format($row->fill_odometer, 0, '.', ',');
            })
            ->editColumn('fill_timestamp', function($row) {
                return $row->fill_timestamp ? Carbon::parse($row->fill_timestamp)->format('d-m-Y H:i:s') : '-';
            })
            ->editColumn('accurate', function($row) {
                return $row->accurate ? 'Yes' : 'No';
            })
            ->make(true);
    }

    public function export(Request $request)
    {
        $fileName = 'Report_Fuel_Fill_' . now()->format('Ymd_His') . '.xlsx';
        return Excel::download(new FuelFillExport($request), $fileName);
    }
}