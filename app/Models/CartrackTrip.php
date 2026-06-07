<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartrackTrip extends Model
{
    protected $guarded = ['id'];

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
