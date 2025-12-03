<?php

namespace Modules\Logistics\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Logistics\DTOs\TripDto;
use Modules\Logistics\Models\Trip;

interface TripServiceInterface
{
    public function startTrip(TripDto $data): Trip;

    public function completeTrip(int $tripId, int $driverId): ?Trip;

    public function getActiveTrips(?int $userId = null, int $perPage = 15): LengthAwarePaginator;

    public function find(int $id): ?Trip;

    public function getActiveTripForDriver(int $driverId): ?Trip;
}
