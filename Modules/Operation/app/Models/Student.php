<?php

namespace Modules\Operation\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};
use Modules\Operation\Database\Factories\StudentFactory;
use Modules\User\Models\User;

class Student extends Model
{
    use HasFactory;

    protected $table = 'students';

    protected $fillable = [
        'user_id',
        'institution_course_id',
        'city_of_origin',
        'status',
        'qrcode_token',
    ];

    protected $with = [
        'user',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function boardings(): HasMany
    {
        return $this->hasMany(\Modules\Logistics\Models\Boarding::class);
    }

    protected static function newFactory(): StudentFactory
    {
        return StudentFactory::new();
    }
}
