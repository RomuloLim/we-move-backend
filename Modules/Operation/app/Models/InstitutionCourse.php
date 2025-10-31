<?php

namespace Modules\Operation\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class InstitutionCourse extends Pivot
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
}
