<?php

namespace Modules\Logistics\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Logistics\Database\Factories\VehicleFactory;

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

    public function licensePlate(): Attribute
    {
        return Attribute::make(
            set: fn (string $value) => strtoupper($value),
        );
    }
}
