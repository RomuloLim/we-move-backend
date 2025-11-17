<?php

namespace Modules\Operation\Repositories\Route;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Operation\DTOs\RouteDto;
use Modules\Operation\Models\Route;

interface RouteRepositoryInterface
{
    public function paginate(int $perPage = 15): LengthAwarePaginator;

    public function all(): array;

    public function find(int $id): ?Route;

    public function create(RouteDto $data): Route;

    public function update(int $id, RouteDto $data): ?Route;

    public function delete(int $id): bool;
}
