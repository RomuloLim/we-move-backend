<?php

namespace Modules\Communication\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, BelongsToMany};
use Modules\Communication\Database\Factories\NoticeFactory;
use Modules\Communication\Enums\NoticeType;
use Modules\Logistics\Models\Route;
use Modules\User\Models\User;

class Notice extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'author_user_id',
        'title',
        'content',
        'type',
        'route_id',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'type' => NoticeType::class,
        ];
    }

    /**
     * Get the author user that owns the notice.
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_user_id');
    }

    /**
     * Get the route associated with the notice.
     */
    public function route(): BelongsTo
    {
        return $this->belongsTo(Route::class);
    }

    /**
     * Get the users that have read this notice.
     */
    public function readByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'notice_reads')
            ->withPivot('read_at')
            ->withTimestamps();
    }

    /**
     * Check if the notice is a general notice.
     */
    public function isGeneral(): bool
    {
        return $this->type === NoticeType::General;
    }

    /**
     * Check if the notice is a route alert.
     */
    public function isRouteAlert(): bool
    {
        return $this->type === NoticeType::RouteAlert;
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): NoticeFactory
    {
        return NoticeFactory::new();
    }
}
