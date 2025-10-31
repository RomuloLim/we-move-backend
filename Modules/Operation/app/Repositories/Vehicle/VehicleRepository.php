<?php

namespace Modules\Operation\Repositories\Vehicle;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Operation\Models\Vehicle;

class VehicleRepository implements VehicleRepositoryInterface
{
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Vehicle::query()->paginate($perPage);
    }

    public function all(): array
    {
        return Vehicle::all()->toArray();
    }

    public function find(int $id): ?Vehicle
    {
        return Vehicle::find($id);
    }

    public function create(array $data): Vehicle
    {
        return Vehicle::create($data);
    }

    public function update(int $id, array $data): ?Vehicle
    {
        $vehicle = Vehicle::find($id);

        if ($vehicle) {
            $vehicle->update($data);
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
