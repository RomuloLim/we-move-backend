<?php

namespace Modules\Operation\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Operation\DTOs\StopDto;
use Modules\Operation\Models\Stop;

interface StopServiceInterface
{
    public function paginate(int $perPage = 15): LengthAwarePaginator;

    public function all(): array;

    public function find(int $id): ?Stop;

    public function create(StopDto $data): Stop;

    public function delete(int $id): bool;

    /**
     * Update the order of multiple stops
     *
     * @param  array<int, array{stop_id: int, order: int}>  $stopsOrder
     */
    public function updateOrder(array $stopsOrder): bool;
}
