<?php

namespace Modules\Operation\Services;

use Modules\Operation\Models\InstitutionCourse;

class InstitutionCourseService implements InstitutionCourseServiceInterface
{
    public function linkCourse(int $institutionId, int $courseId): ?InstitutionCourse
    {
        $existing = InstitutionCourse::where('institution_id', $institutionId)
            ->where('course_id', $courseId)
            ->first();

        if ($existing) {
            return $existing;
        }

        return InstitutionCourse::create([
            'institution_id' => $institutionId,
            'course_id' => $courseId,
        ]);
    }

    public function unlinkCourse(int $institutionId, int $courseId): bool
    {
        $link = InstitutionCourse::where('institution_id', $institutionId)
            ->where('course_id', $courseId)
            ->first();

        if (!$link) {
            return false;
        }

        return (bool) $link->delete();
    }
}
