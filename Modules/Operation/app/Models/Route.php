<?php

namespace Modules\Operation\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Operation\Database\Factories\RouteFactory;
use Modules\User\Models\User;

// use Modules\Operation\Database\Factories\RouteFactory;

class Route extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'route_name',
        'description'
    ];

     protected static function newFactory(): RouteFactory
     {
          return RouteFactory::new();
     }

    public function stops(): HasMany
    {
        return $this->hasMany(Stop::class, 'route_id');
    }
}
