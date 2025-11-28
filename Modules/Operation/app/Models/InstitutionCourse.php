<?php

namespace Modules\Operation\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};

class InstitutionCourse extends Model
{
    protected $table = 'institution_courses';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'institution_id',
        'course_id',
    ];

    /**
     * Get the institution associated with this record.
     */
    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    /**
     * Get the course associated with this record.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the student requisitions for this institution course.
     */
    public function studentRequisitions(): HasMany
    {
        return $this->hasMany(StudentRequisition::class);
    }
}
