<?php

namespace Modules\Logistics\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Logistics\Database\Factories\StopFactory;

// use Modules\Logistics\Database\Factories\StopFactory;

class Stop extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'route_id',
        'stop_name',
        'latitude',
        'longitude',
        'scheduled_time',
        'order',
    ];

    protected static function newFactory(): StopFactory
    {
        return StopFactory::new();
    }

    public function route(): BelongsTo
    {
        return $this->belongsTo(Route::class, 'route_id');
    }
}
