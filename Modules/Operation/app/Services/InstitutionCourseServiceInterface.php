<?php

namespace Modules\Operation\Services;

use Modules\Operation\Models\InstitutionCourse;

interface InstitutionCourseServiceInterface
{
    public function linkCourse(int $institutionId, int $courseId): ?InstitutionCourse;
    public function unlinkCourse(int $institutionId, int $courseId): bool;
}
