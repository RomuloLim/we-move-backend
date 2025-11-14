<?php

namespace Modules\Operation\Repositories\Course;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Operation\DTOs\CourseDto;
use Modules\Operation\Models\Course;

class CourseRepository implements CourseRepositoryInterface
{
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Course::query()->paginate($perPage);
    }

    public function all(): array
    {
        return Course::all()->toArray();
    }

    public function getOrderedByInstitution(int $institutionId): LengthAwarePaginator
    {
        $courses = Course::query()
            ->select('courses.*')
            ->leftJoin('institution_courses', 'courses.id', '=', 'institution_courses.course_id')
            ->where('institution_courses.institution_id', $institutionId)
            ->orWhereNull('institution_courses.institution_id')
            ->orderByRaw('CASE WHEN institution_courses.institution_id IS NOT NULL THEN 0 ELSE 1 END');

        return $courses->paginate();
    }

    public function find(int $id): ?Course
    {
        return Course::find($id);
    }

    public function create(CourseDto $data): Course
    {
        return Course::create($data->toArray());
    }

    public function update(int $id, CourseDto $data): ?Course
    {
        $course = Course::find($id);

        if ($course) {
            $course->update($data->toArray());
        }

        return $course;
    }

    public function delete(int $id): bool
    {
        $course = Course::find($id);

        if ($course) {
            return (bool) $course->delete();
        }

        return false;
    }
}
