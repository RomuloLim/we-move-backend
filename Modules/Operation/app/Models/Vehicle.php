<?php

namespace Modules\Operation\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Operation\Database\Factories\VehicleFactory;

class Vehicle extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'license_plate',
        'model',
        'capacity',
    ];

    /**
     * Get the factory for the model.
     */
    protected static function newFactory(): VehicleFactory
    {
        return VehicleFactory::new();
    }
}
