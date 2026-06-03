<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FuelConsumedLevel extends Model
{
    protected $table = 'fuel_consumed_levels';

    protected $fillable = [
        'vehicle_id',
        'registration',
        'start_period_liters',
        'start_period_timestamp',
        'end_period_liters',
        'end_period_timestamp',
        'estimated_fuel_used',
    ];

    // Opsional: Casting untuk memastikan tipe data saat diakses
    protected $casts = [
        'start_period_timestamp' => 'datetime',
        'end_period_timestamp' => 'datetime',
        'start_period_liters' => 'float',
        'end_period_liters' => 'float',
        'estimated_fuel_used' => 'float',
    ];
}
