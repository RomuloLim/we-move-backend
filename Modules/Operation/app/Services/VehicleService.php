<?php

namespace Modules\Operation\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Operation\Models\Vehicle;
use Modules\Operation\Repositories\Vehicle\VehicleRepositoryInterface;

class VehicleService implements VehicleServiceInterface
{
    public function __construct(protected VehicleRepositoryInterface $repository) {}

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage);
    }

    public function all(): array
    {
        return $this->repository->all();
    }

    public function find(int $id): ?Vehicle
    {
        return $this->repository->find($id);
    }

    public function create(array $data): Vehicle
    {
        return $this->repository->create($data);
    }

    public function update(int $id, array $data): ?Vehicle
    {
        return $this->repository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }
}
