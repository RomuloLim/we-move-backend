<?php

namespace Modules\Operation\Repositories\Stop;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Operation\DTOs\StopDto;
use Modules\Operation\Models\Stop;

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
            ->max('stop_order') ?? 0;

        return Stop::create([
            'stop_order' => $latestStopOrder + 1,
            ...$data->toArray(),
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
