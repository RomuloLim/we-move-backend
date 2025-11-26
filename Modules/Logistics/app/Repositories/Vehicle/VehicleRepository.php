<?php

namespace Modules\Logistics\Repositories\Vehicle;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Modules\Logistics\DTOs\VehicleDto;
use Modules\Logistics\Models\Vehicle;

class VehicleRepository implements VehicleRepositoryInterface
{
    public function paginate(?string $search = null, int $perPage = 15): LengthAwarePaginator
    {
        return Vehicle::query()
            ->when($search, function (Builder $query) use ($search) {
                $query->where('license_plate', 'ilike', $search)
                    ->orWhere('model', 'ilike', "%$search%");
            })
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    public function all(): array
    {
        return Vehicle::all()->toArray();
    }

    public function find(int $id): ?Vehicle
    {
        return Vehicle::find($id);
    }

    public function create(VehicleDto $data): Vehicle
    {
        return Vehicle::create($data->toArray());
    }

    public function update(int $id, VehicleDto $data): ?Vehicle
    {
        $vehicle = Vehicle::find($id);

        if ($vehicle) {
            $vehicle->update($data->toArray());
        }

        return $vehicle;
    }

    public function delete(int $id): bool
    {
        $vehicle = Vehicle::find($id);

        if ($vehicle) {
            return (bool) $vehicle->delete();
        }

        return false;
    }
}
