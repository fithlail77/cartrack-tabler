<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    protected $table = 'vehicles';

    protected $fillable = [
        'vehicle_id',
        'registration',
        'vehicle_name',
        'max_speed',
        'manufacturer',
        'model',
        'model_year',
        'colour',
        'chassis_number',
        'vehicle_type',
        'client_vehicle_description',
    ];
}
