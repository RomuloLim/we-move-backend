<?php

namespace Modules\Operation\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\QueryException;
use Modules\Operation\DTOs\{CourseDto};
use Modules\Operation\Models\{Course, StudentRequisition};
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
        $hasStudentRequisitions = StudentRequisition::whereHas('institutionCourse', function ($query) use ($id) {
            $query->where('course_id', $id);
        })->exists();

        if ($hasStudentRequisitions) {
            throw new \Exception('Não é possível remover este curso pois existem requisições de alunos vinculadas a ele.');
        }

        try {
            return $this->repository->delete($id);
        } catch (QueryException $e) {
            if ($e->getCode() === '23503') {
                throw new \Exception('Não é possível remover este curso pois existem requisições de alunos vinculadas a ele.');
            }

            throw $e;
        }
    }
}
