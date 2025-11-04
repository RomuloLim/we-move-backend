<?php

namespace Modules\Operation\Repositories\Vehicle;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Operation\DTOs\VehicleDto;
use Modules\Operation\Models\Vehicle;

interface VehicleRepositoryInterface
{
    public function paginate(string $search = null, int $perPage = 15): LengthAwarePaginator;

    /**
     * @return Vehicle[]
     */
    public function all(): array;

    public function find(int $id): ?Vehicle;

    public function create(VehicleDto $data): Vehicle;

    public function update(int $id, VehicleDto $data): ?Vehicle;

    public function delete(int $id): bool;
}
