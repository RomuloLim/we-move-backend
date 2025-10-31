<?php

namespace Modules\Operation\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Operation\DTOs\InstitutionDto;
use Modules\Operation\Models\Institution;
use Modules\Operation\Repositories\Institution\InstitutionRepositoryInterface;

class InstitutionService implements InstitutionServiceInterface
{
    public function __construct(protected InstitutionRepositoryInterface $repository) {}

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage);
    }

    public function all(): array
    {
        return $this->repository->all();
    }

    public function find(int $id): ?Institution
    {
        return $this->repository->find($id);
    }

    public function create(InstitutionDto $data): Institution
    {
        return $this->repository->create($data);
    }

    public function update(int $id, InstitutionDto $data): ?Institution
    {
        return $this->repository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }
}
