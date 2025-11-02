<?php

namespace Modules\Operation\Services;

use Illuminate\Support\Collection;
use Modules\Operation\DTOs\CourseDto;

interface InstitutionCourseServiceInterface
{
    /**
     * @param  array<int>  $coursesIds
     * @return Collection<int, CourseDto>
     */
    public function linkCourse(int $institutionId, array $coursesIds): Collection;

    /**
     * @param  array<int>  $coursesIds
     */
    public function unlinkCourse(int $institutionId, array $coursesIds): bool;
}
