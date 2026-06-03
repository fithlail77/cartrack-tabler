<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleActivity extends Model
{
    protected $fillable = [
        'vehicle_id',
        'activity_date',
        'registration',
        'chassis_number',
        'first_ignition_on',
        'last_ignition_off',
        'idle_time_seconds',
        'driving_time_seconds',
        'total_working_hours',
        'total_break_hours',
        'total_break_time_trimmed',
        'driver_id',
        'first_name',
        'last_name',
    ];

    protected function casts(): array
    {
        return [
            'activity_date' => 'date',
        ];
    }
}
