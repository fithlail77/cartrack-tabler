<?php

namespace App\Exports;

use App\Models\CartrackTrip;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Carbon\Carbon;

class TripExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function query()
    {
        $query = CartrackTrip::query();

        if ($this->request->start_date && $this->request->end_date) {
            $query->whereDate('start_timestamp', '>=', $this->request->start_date)
                  ->whereDate('start_timestamp', '<=', $this->request->end_date);
        }

        $query->when($this->request->registration, function ($q) {
            return $q->where('registration', $this->request->registration);
        });

        return $query->orderBy('start_timestamp', 'desc');
    }

    public function headings(): array
    {
        return [
            'Registration',
            'Start Time',
            'End Time',
            'Duration (seconds)',
            'Trip Distance (km)',
            'Start Odometer',
            'End Odometer',
            'Max Speed',
            'Idle Time (seconds)',
            'Driver Name',
            'Start Location',
            'End Location',
        ];
    }

    public function map($trip): array
    {
        $driverName = trim(($trip->driver_name ?? '') . ' ' . ($trip->driver_surname ?? ''));

        return [
            $trip->registration,
            $trip->start_timestamp ? Carbon::parse($trip->start_timestamp)->format('d-m-Y H:i:s') : '-',
            $trip->end_timestamp ? Carbon::parse($trip->end_timestamp)->format('d-m-Y H:i:s') : '-',
            $trip->trip_duration_seconds ?? '-',
            number_format(($trip->trip_distance ?? 0) / 1000, 2, '.', ''),
            $trip->start_odometer ?? '-',
            $trip->end_odometer ?? '-',
            $trip->max_speed ?? '-',
            $trip->idle_time_seconds ?? '-',
            $driverName ?: '-',
            $trip->start_location ?? '-',
            $trip->end_location ?? '-',
        ];
    }
}