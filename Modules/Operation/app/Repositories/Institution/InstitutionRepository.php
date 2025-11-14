<?php

namespace Modules\Operation\Repositories\Institution;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Operation\DTOs\InstitutionDto;
use Modules\Operation\Models\Institution;

class InstitutionRepository implements InstitutionRepositoryInterface
{
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Institution::query()->paginate($perPage);
    }

    public function getOrderedByCourse(int $courseId): LengthAwarePaginator
    {
        $institutions = Institution::query()
            ->select('institutions.*')
            ->leftJoin('institution_courses', 'institutions.id', '=', 'institution_courses.institution_id')
            ->where('institution_courses.course_id', $courseId)
            ->orWhereNull('institution_courses.course_id')
            ->orderByRaw("CASE WHEN institution_courses.course_id IS NOT NULL THEN 0 ELSE 1 END");

        return $institutions->paginate();
    }

    public function all(): array
    {
        return Institution::all()->toArray();
    }

    public function find(int $id): ?Institution
    {
        return Institution::query()->find($id);
    }

    public function findOrFail(int $id): ?Institution
    {
        return Institution::query()->findOrFail($id);
    }

    public function create(InstitutionDto $data): Institution
    {
        return Institution::create($data->toArray());
    }

    public function update(int $id, InstitutionDto $data): ?Institution
    {
        $institution = Institution::find($id);

        if ($institution) {
            $institution->update($data->toArray());
        }

        return $institution;
    }

    public function delete(int $id): bool
    {
        $institution = Institution::find($id);

        if ($institution) {
            return (bool) $institution->delete();
        }

        return false;
    }

    public function getByCourseId(int $courseId): LengthAwarePaginator
    {
        $institutions = Institution::query()
            ->whereHas('courses', function ($query) use ($courseId) {
                $query->where('courses.id', $courseId);
            });

        return $institutions->paginate();
    }
}
