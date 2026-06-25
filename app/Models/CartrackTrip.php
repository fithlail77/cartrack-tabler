<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartrackTrip extends Model
{
    protected $fillable = [
        'trip_id',
        'vehicle_id',
        'registration',
        'start_timestamp',
        'end_timestamp',
        'trip_duration',
        'trip_duration_seconds',
        'idle_time',
        'idle_time_seconds',
        'clock_start',
        'clock_end',
        'start_location',
        'end_location',
        'start_odometer',
        'end_odometer',
        'trip_distance',
        'start_geofence_name',
        'end_geofence_name',
        'max_speed',
        'driver_id',
        'driver_name',
        'driver_surname',
        'start_coordinates',
        'end_coordinates',
    ];

    // Cast JSON data otomatis menjadi Array di PHP
    protected function casts(): array
    {
        return [
            'start_coordinates' => 'array',
            'end_coordinates' => 'array',
            'start_timestamp' => 'datetime',
            'end_timestamp' => 'datetime',
        ];
    }
}
