<?php

namespace Modules\Logistics\Repositories\Route;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Logistics\DTOs\RouteDto;
use Modules\Logistics\Models\Route;

class RouteRepository implements RouteRepositoryInterface
{
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Route::query()
            ->with(['firstStop', 'lastStop'])
            ->withCount('stops as stops_amount')
            ->paginate($perPage);
    }

    public function all(): array
    {
        return Route::with('stops')->get()->toArray();
    }

    public function find(int $id): ?Route
    {
        return Route::with('stops')->find($id);
    }

    public function create(RouteDto $data): Route
    {
        return Route::create($data->toArray());
    }

    public function update(int $id, RouteDto $data): ?Route
    {
        $route = $this->find($id);

        if (!$route) {
            return null;
        }

        $route->update($data->toArray());

        return $route->fresh();
    }

    public function delete(int $id): bool
    {
        $route = $this->find($id);

        if (!$route) {
            return false;
        }

        return $route->delete();
    }
}
