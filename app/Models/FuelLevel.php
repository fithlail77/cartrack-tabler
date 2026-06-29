<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FuelLevel extends Model
{
    protected $fillable = [
        'vehicle_id',
        'start_liters',
        'start_timestamp',
        'end_liters',
        'end_timestamp',
        'estimated_fuel_used',
        'calibrated',
        'registration',
    ];

    protected $casts = [
        'start_timestamp' => 'datetime',
        'end_timestamp' => 'datetime',
        'calibrated' => 'boolean',
    ];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class, 'registration', 'registration');
    }
}
