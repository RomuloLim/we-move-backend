<?php

namespace Modules\Logistics\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Logistics\DTOs\VehicleDto;
use Modules\Logistics\Models\Vehicle;

interface VehicleServiceInterface
{
    public function paginate(?string $search = null, int $perPage = 15): LengthAwarePaginator;
    public function all(): array;
    public function find(int $id): ?Vehicle;
    public function create(VehicleDto $data): Vehicle;
    public function update(int $id, VehicleDto $data): ?Vehicle;
    public function delete(int $id): bool;
}
