<?php

namespace Modules\Operation\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Operation\DTOs\CourseDto;
use Modules\Operation\Models\Course;

interface CourseServiceInterface
{
    public function paginate(int $perPage = 15): LengthAwarePaginator;
    public function all(): array;
    public function find(int $id): ?Course;
    public function create(CourseDto $data): Course;
    public function update(int $id, CourseDto $data): ?Course;
    public function delete(int $id): bool;
    public function getByInstitutionId(int $institutionId): array;
}
