<?php

namespace Modules\Operation\Services;

use Illuminate\Support\Collection;
use Modules\Operation\DTOs\CourseDto;
use Modules\Operation\Repositories\Institution\InstitutionRepositoryInterface;

class InstitutionCourseService implements InstitutionCourseServiceInterface
{
    public function __construct(private readonly InstitutionRepositoryInterface $institutionRepository)
    {
    }

    public function linkCourse(int $institutionId, array $coursesIds): Collection
    {
        $institution = $this->institutionRepository->findOrFail($institutionId);

        $institution->courses()->syncWithoutDetaching($coursesIds);

        $courses = $institution->courses()->get();

        $courseCollection = CourseDto::collection($courses->toArray());

        return $courseCollection;
    }

    public function unlinkCourse(int $institutionId, array $coursesIds): bool
    {
        $institution = $this->institutionRepository->findOrFail($institutionId);

        $deleted = $institution->courses()->detach($coursesIds);

        return (bool) $deleted;
    }
}
