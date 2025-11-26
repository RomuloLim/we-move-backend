<?php

namespace Modules\Logistics\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Logistics\DTOs\RouteDto;
use Modules\Logistics\Models\Route;

interface RouteServiceInterface
{
    public function paginate(int $perPage = 15): LengthAwarePaginator;

    public function all(): array;

    public function find(int $id): ?Route;

    public function create(RouteDto $data): Route;

    public function update(int $id, RouteDto $data): ?Route;

    public function delete(int $id): bool;
}
