<?php

namespace Modules\Logistics\Repositories\Stop;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Logistics\DTOs\StopDto;
use Modules\Logistics\Models\Stop;

class StopRepository implements StopRepositoryInterface
{
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Stop::query()->with('route')->paginate($perPage);
    }

    public function all(): array
    {
        return Stop::with('route')->get()->toArray();
    }

    public function find(int $id): ?Stop
    {
        return Stop::with('route')->find($id);
    }

    public function create(StopDto $data): Stop
    {
        $latestStopOrder = Stop::where('route_id', $data->routeId)
            ->max('order') ?? 0;

        return Stop::create([
            ...$data->toArray(),
            'order' => $latestStopOrder + 1,
        ]);
    }

    public function delete(int $id): bool
    {
        $stop = $this->find($id);

        if (!$stop) {
            return false;
        }

        return $stop->delete();
    }

    public function updateOrder(array $stopsOrder): bool
    {
        foreach ($stopsOrder as $item) {
            Stop::where('id', $item['stop_id'])->update(['order' => $item['order']]);
        }

        return true;
    }
}
