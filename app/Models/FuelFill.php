<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FuelFill extends Model
{
    protected $table = 'fuel_fills';

    protected $fillable = [
        'vehicle_id',
        'registration',
        'fill_ammount_litres',
        'fill_timestamp',
        'fill_odometer',
        'fill_location',
        'latitude',
        'longitude',
        'accurate',
    ];

    // Casting tipe data PostgreSQL
    protected $casts = [
        'fill_timestamp' => 'datetime',
        'accurate' => 'boolean',
        'fill_ammount_litres' => 'decimal:2',
        'fill_odometer' => 'decimal:2',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
    
}
