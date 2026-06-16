<?php

namespace App\Exports;

use App\Models\VehicleActivity;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Http\Request;

class VehicleActivityExport implements FromQuery, WithHeadings, WithMapping
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function query()
    {
        $query = VehicleActivity::query();

        if ($this->request->filled('start_date') && $this->request->filled('end_date')) {
            $query->whereBetween('activity_date', [$this->request->start_date, $this->request->end_date]);
        }

        if ($this->request->filled('registration')) {
            $query->where('registration', $this->request->registration);
        }

        return $query->orderBy('activity_date', 'desc');
    }

    public function headings(): array
    {
        return [
            'Registrasi', 'Tanggal', 'First Ignition On', 'Last Ignition Off',
            'Idle Time', 'Driving Time', 'Working Hours', 'Break Hours', 'Driver'
        ];
    }

    public function map($activity): array
    {
        return [
            $activity->registration,
            $activity->activity_date,
            $activity->first_ignition_on,
            $activity->last_ignition_off,
            gmdate("H:i:s", $activity->idle_time_seconds),
            gmdate("H:i:s", $activity->driving_time_seconds),
            $activity->total_working_hours,
            $activity->total_break_hours,
            $activity->first_name . ' ' . $activity->last_name,
        ];
    }
}