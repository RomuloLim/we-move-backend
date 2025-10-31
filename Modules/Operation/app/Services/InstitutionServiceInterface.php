<?php

namespace Modules\Operation\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Operation\DTOs\InstitutionDto;
use Modules\Operation\Models\Institution;

interface InstitutionServiceInterface
{
    public function paginate(int $perPage = 15): LengthAwarePaginator;
    public function all(): array;
    public function find(int $id): ?Institution;
    public function create(InstitutionDto $data): Institution;
    public function update(int $id, InstitutionDto $data): ?Institution;
    public function delete(int $id): bool;
}
