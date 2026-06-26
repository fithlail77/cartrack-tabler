<?php

namespace App\Exports;

use App\Models\FuelFill;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FuelFillExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function query()
    {
        $query = FuelFill::query();

        if ($this->request->start_date && $this->request->end_date) {
            $query->whereDate('fill_timestamp', '>=', $this->request->start_date)
                  ->whereDate('fill_timestamp', '<=', $this->request->end_date);
        }

        $query->when($this->request->registration, function ($q) {
            return $q->where('registration', $this->request->registration);
        });

        return $query->orderBy('fill_timestamp', 'desc');
    }

    public function headings(): array
    {
        return [
            'Registration',
            'Fill Amount (L)',
            'Fill Odometer',
            'Fill Timestamp',
            'Fill Location',
            'Latitude',
            'Longitude',
            'Accurate',
        ];
    }

    public function map($fuel): array
    {
        return [
            $fuel->registration,
            number_format($fuel->fill_ammount_litres, 2, '.', ''),
            number_format($fuel->fill_odometer, 0, '.', ','),
            $fuel->fill_timestamp ? Carbon::parse($fuel->fill_timestamp)->format('d-m-Y H:i:s') : '-',
            $fuel->fill_location ?? '-',
            $fuel->latitude ?? '-',
            $fuel->longitude ?? '-',
            $fuel->accurate ? 'Yes' : 'No',
        ];
    }
}
