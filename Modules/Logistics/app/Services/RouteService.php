<?php

namespace Modules\Logistics\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Logistics\DTOs\RouteDto;
use Modules\Logistics\Models\Route;
use Modules\Logistics\Repositories\Route\RouteRepositoryInterface;

class RouteService implements RouteServiceInterface
{
    public function __construct(protected RouteRepositoryInterface $repository) {}

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage);
    }

    public function all(): array
    {
        return $this->repository->all();
    }

    public function find(int $id): ?Route
    {
        return $this->repository->find($id);
    }

    public function create(RouteDto $data): Route
    {
        return $this->repository->create($data);
    }

    public function update(int $id, RouteDto $data): ?Route
    {
        return $this->repository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }
}
