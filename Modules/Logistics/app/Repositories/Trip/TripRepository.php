<?php

namespace Modules\Logistics\Repositories\Trip;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Logistics\DTOs\TripDto;
use Modules\Logistics\Enums\TripStatus;
use Modules\Logistics\Models\Trip;

class TripRepository implements TripRepositoryInterface
{
    public function create(TripDto $data): Trip
    {
        return Trip::create($data->toArray());
    }

    public function update(int $id, array $data): ?Trip
    {
        $trip = Trip::find($id);

        if (!$trip) {
            return null;
        }

        $trip->update($data);

        return $trip->fresh();
    }

    public function find(int $id): ?Trip
    {
        return Trip::with(['route', 'driver', 'vehicle'])
            ->find($id);
    }

    public function getActiveTrips(?int $userId = null, int $perPage = 15): LengthAwarePaginator
    {
        $query = Trip::query()
            ->with(['route.lastStop', 'driver', 'vehicle'])
            ->where('status', TripStatus::InProgress);

        if ($userId) {
            $query->whereHas('route.userRoutes', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            });
        }

        return $query->orderBy('trip_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function hasActiveTrip(int $driverId): bool
    {
        return Trip::where('driver_id', $driverId)
            ->where('status', TripStatus::InProgress)
            ->exists();
    }

    public function hasActiveVehicle(int $vehicleId): bool
    {
        return Trip::where('vehicle_id', $vehicleId)
            ->where('status', TripStatus::InProgress)
            ->exists();
    }

    public function findByDriverAndStatus(int $driverId, TripStatus $status): ?Trip
    {
        return Trip::with(['route', 'driver', 'vehicle'])
            ->where('driver_id', $driverId)
            ->where('status', $status)
            ->first();
    }
}
