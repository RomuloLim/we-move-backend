<?php

namespace Modules\Logistics\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Logistics\DTOs\StopDto;
use Modules\Logistics\Models\Stop;
use Modules\Logistics\Repositories\Stop\StopRepositoryInterface;

class StopService implements StopServiceInterface
{
    public function __construct(protected StopRepositoryInterface $repository) {}

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage);
    }

    public function all(): array
    {
        return $this->repository->all();
    }

    public function find(int $id): ?Stop
    {
        return $this->repository->find($id);
    }

    public function create(StopDto $data): Stop
    {
        return $this->repository->create($data);
    }

    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }

    public function updateOrder(array $stopsOrder): bool
    {
        return $this->repository->updateOrder($stopsOrder);
    }
}
