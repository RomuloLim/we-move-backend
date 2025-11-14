<?php

namespace Modules\Operation\Services;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Modules\Operation\DTOs\{CourseDto};
use Modules\Operation\Repositories\Course\CourseRepositoryInterface;
use Modules\Operation\Repositories\Institution\InstitutionRepositoryInterface;

class InstitutionCourseService implements InstitutionCourseServiceInterface
{
    public function __construct(
        private readonly InstitutionRepositoryInterface $institutionRepository,
        private readonly CourseRepositoryInterface $courseRepository
    ) {}

    public function getInstitutionsOrderedByCourse(int $courseId): LengthAwarePaginator
    {
        return $this->institutionRepository->getOrderedByCourse($courseId);
    }

    public function getCoursesOrderedByInstitution(int $institutionId): LengthAwarePaginator
    {
        return $this->courseRepository->getOrderedByInstitution($institutionId);
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
