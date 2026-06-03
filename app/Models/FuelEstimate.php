<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FuelEstimate extends Model
{
    protected $table = 'fuel_estimates';
    
    protected $fillable = [
        'vehicle_id', 'registration', 'start_timestamp', 'end_timestamp',
        'start_liters', 'end_liters', 'estimated_fuel_used', 'calibrated'
    ];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }
}
