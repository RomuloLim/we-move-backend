<?php

namespace Modules\Logistics\Repositories\Trip;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Logistics\DTOs\TripDto;
use Modules\Logistics\Enums\TripStatus;
use Modules\Logistics\Models\Trip;

interface TripRepositoryInterface
{
    public function create(TripDto $data): Trip;

    public function update(int $id, array $data): ?Trip;

    public function find(int $id): ?Trip;

    public function getActiveTrips(?int $userId = null, int $perPage = 15): LengthAwarePaginator;

    public function hasActiveTrip(int $driverId): bool;

    public function hasActiveVehicle(int $vehicleId): bool;

    public function findByDriverAndStatus(int $driverId, TripStatus $status): ?Trip;

    public function findActiveTripForStudent(int $studentId): ?Trip;
}
