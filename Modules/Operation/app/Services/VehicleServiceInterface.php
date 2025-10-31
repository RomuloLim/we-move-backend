<?php

namespace Modules\Operation\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Operation\Models\Vehicle;

interface VehicleServiceInterface
{
    public function paginate(int $perPage = 15): LengthAwarePaginator;
    public function all(): array;
    public function find(int $id): ?Vehicle;
    public function create(array $data): Vehicle;
    public function update(int $id, array $data): ?Vehicle;
    public function delete(int $id): bool;
}
