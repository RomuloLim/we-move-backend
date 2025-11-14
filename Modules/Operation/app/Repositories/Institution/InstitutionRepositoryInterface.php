<?php

namespace Modules\Operation\Repositories\Institution;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Operation\DTOs\InstitutionDto;
use Modules\Operation\Models\Institution;

interface InstitutionRepositoryInterface
{
    public function paginate(int $perPage = 15): LengthAwarePaginator;

    public function getOrderedByCourse(int $courseId): LengthAwarePaginator;

    /**
     * @return Institution[]
     */
    public function all(): array;

    public function find(int $id): ?Institution;

    public function findOrFail(int $id): ?Institution;

    public function getByCourseId(int $courseId): LengthAwarePaginator;

    public function update(int $id, InstitutionDto $data): ?Institution;

    public function delete(int $id): bool;
}
