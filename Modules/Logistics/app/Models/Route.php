<?php

namespace Modules\Logistics\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsToMany, HasMany, HasOne};
use Modules\Logistics\Database\Factories\RouteFactory;
use Modules\User\Models\User;

// use Modules\Logistics\Database\Factories\RouteFactory;

class Route extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'route_name',
        'description',
    ];

    protected static function newFactory(): RouteFactory
    {
        return RouteFactory::new();
    }

    public function stops(): HasMany
    {
        return $this->hasMany(Stop::class, 'route_id');
    }

    public function firstStop(): HasOne
    {
        return $this->hasOne(Stop::class)->ofMany('order', 'min');
    }

    public function lastStop(): HasOne
    {
        return $this->hasOne(Stop::class)->ofMany('order', 'max');
    }

    public function trips(): HasMany
    {
        return $this->hasMany(Trip::class);
    }

    public function userRoutes(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_routes')
            ->withTimestamps();
    }
}
