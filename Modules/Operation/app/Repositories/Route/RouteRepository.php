<?php

namespace Modules\Operation\Repositories\Route;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Operation\Models\Route;

class RouteRepository implements RouteRepositoryInterface
{
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Route::query()->paginate($perPage);
    }

    public function all(): array
    {
        return Route::all()->toArray();
    }

    public function find(int $id): ?Route
    {
        return Route::find($id);
    }
}
