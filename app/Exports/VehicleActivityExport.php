<?php

namespace App\Exports;

use App\Models\VehicleActivity;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class VehicleActivityExport implements FromQuery, WithHeadings, WithMapping
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function query()
    {
        $query = VehicleActivity::query();

        // Filter Range Tanggal
        if ($this->request->start_date && $this->request->end_date) {
            $query->whereDate('activity_date', '>=', $this->request->start_date)
                  ->whereDate('activity_date', '<=', $this->request->end_date);
        }

        // Filter Registrasi (Opsional)
        $query->when($this->request->filled('registration'), function ($q) {
            return $q->where('registration', $this->request->registration);
        });

        return $query;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'Registrasi',
            'Tanggal',
            'Idle Time',
            'Driving Time',
            'Working Hours',
            'Driver',
        ];
    }

    /**
     * @param mixed $activity
     * @return array
     */
    public function map($activity): array
    {
        // Helper function untuk format detik ke HH:MM:SS
        $formatSeconds = function($seconds) {
            if (!is_numeric($seconds) || $seconds == 0) return "00:00:00";
            return sprintf("%02d:%02d:%02d", floor($seconds / 3600), floor(($seconds % 3600) / 60), floor($seconds % 60));
        };

        return [
            $activity->registration,
            $activity->activity_date,
            $formatSeconds($activity->idle_time_seconds),
            $formatSeconds($activity->driving_time_seconds),
            $activity->total_working_hours ?? '00:00:00',
            $activity->driver_id ?? '-',
        ];
    }
}