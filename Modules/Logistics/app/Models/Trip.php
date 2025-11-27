<?php

namespace Modules\Logistics\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Logistics\Database\Factories\TripFactory;
use Modules\Logistics\Enums\TripStatus;
use Modules\User\Models\User;

class Trip extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'route_id',
        'driver_id',
        'vehicle_id',
        'trip_date',
        'status',
    ];

    protected static function newFactory(): TripFactory
    {
        return TripFactory::new();
    }

    protected function casts(): array
    {
        return [
            'trip_date' => 'date',
            'status' => TripStatus::class,
        ];
    }

    public function route(): BelongsTo
    {
        return $this->belongsTo(Route::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }
}
