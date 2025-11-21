<?php

namespace Modules\Operation\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Modules\Operation\Database\Factories\CourseFactory;
use Modules\Operation\Enums\CourseType;

class Course extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'course_type',
        'description',
    ];

    protected $casts = [
        'course_type' => CourseType::class,
    ];

    public function name(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => mb_strtoupper($value, 'UTF-8'),
        );
    }

    /**
     * Get the institutions associated with this course.
     */
    public function institutions(): BelongsToMany
    {
        return $this->belongsToMany(Institution::class, 'institution_courses')->withPivot('id');
    }

    /**
     * Get the factory for the model.
     */
    protected static function newFactory(): CourseFactory
    {
        return CourseFactory::new();
    }
}
