<?php

namespace Modules\Operation\Repositories\Stop;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Operation\Models\Stop;

class StopRepository implements StopRepositoryInterface
{
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Stop::query()->paginate($perPage);
    }

    public function all(): array
    {
        return Stop::all()->toArray();
    }
}
