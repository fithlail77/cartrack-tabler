<?php

namespace App\Exports;

use App\Models\FuelLevel;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Carbon\Carbon;

class FuelConsumedExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function query()
    {
        $query = FuelLevel::query();

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
            'Start Liters',
            'Start Period (Jakarta)',
            'End Liters',
            'End Period (Jakarta)',
            'Estimated Fuel Used (L)',
        ];
    }

    public function map($fuel): array
    {
        return [
            $fuel->registration,
            $fuel->start_liters,
            $fuel->start_timestamp ? Carbon::parse($fuel->start_timestamp)->format('d-m-Y H:i:s') : '-',
            $fuel->end_liters,
            $fuel->end_timestamp ? Carbon::parse($fuel->end_timestamp)->format('d-m-Y H:i:s') : '-',
            number_format($fuel->estimated_fuel_used, 2, '.', ''),
        ];
    }
}