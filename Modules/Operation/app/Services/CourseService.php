<?php

namespace Modules\Operation\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Operation\DTOs\CourseDto;
use Modules\Operation\Models\Course;
use Modules\Operation\Models\Institution;
use Modules\Operation\Repositories\Course\CourseRepositoryInterface;

class CourseService implements CourseServiceInterface
{
    public function __construct(protected CourseRepositoryInterface $repository) {}

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage);
    }

    public function all(): array
    {
        return $this->repository->all();
    }

    public function find(int $id): ?Course
    {
        return $this->repository->find($id);
    }

    public function create(CourseDto $data): Course
    {
        return $this->repository->create($data);
    }

    public function update(int $id, CourseDto $data): ?Course
    {
        return $this->repository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }

    public function getByInstitutionId(int $institutionId): array
    {
        $institution = Institution::find($institutionId);
        
        if (!$institution) {
            return [];
        }

        return $institution->courses()->get()->toArray();
    }
}
