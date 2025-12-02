<?php

namespace Modules\Logistics\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Logistics\DTOs\VehicleDto;
use Modules\Logistics\Enums\VehicleAvailabilityFilter;
use Modules\Logistics\Models\Vehicle;
use Modules\Logistics\Repositories\Vehicle\VehicleRepositoryInterface;

class VehicleService implements VehicleServiceInterface
{
    public function __construct(protected VehicleRepositoryInterface $repository) {}

    public function paginate(?string $search = null, int $perPage = 15, ?VehicleAvailabilityFilter $availability = null): LengthAwarePaginator
    {
        return $this->repository->paginate($search, $perPage, $availability);
    }

    public function all(): array
    {
        return $this->repository->all();
    }

    public function find(int $id): ?Vehicle
    {
        return $this->repository->find($id);
    }

    public function create(VehicleDto $data): Vehicle
    {
        return $this->repository->create($data);
    }

    public function update(int $id, VehicleDto $data): ?Vehicle
    {
        return $this->repository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }
}
