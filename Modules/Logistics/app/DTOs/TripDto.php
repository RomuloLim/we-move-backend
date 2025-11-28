<?php

namespace Modules\Logistics\DTOs;

use App\Contracts\DtoContract;
use Illuminate\Support\Collection;
use Modules\Logistics\Enums\TripStatus;

readonly class TripDto implements DtoContract
{
    public function __construct(
        public int $routeId,
        public int $driverId,
        public int $vehicleId,
        public string $tripDate,
        public ?TripStatus $status = null,
    ) {}

    public static function collection(array $data): Collection
    {
        $dtos = array_map(function ($trip) {
            return new TripDto(
                routeId: $trip['route_id'],
                driverId: $trip['driver_id'],
                vehicleId: $trip['vehicle_id'],
                tripDate: $trip['trip_date'],
                status: isset($trip['status']) ? TripStatus::from($trip['status']) : null,
            );
        }, $data);

        return new Collection($dtos);
    }

    public function toArray(): array
    {
        $data = [
            'route_id' => $this->routeId,
            'driver_id' => $this->driverId,
            'vehicle_id' => $this->vehicleId,
            'trip_date' => $this->tripDate,
        ];

        if ($this->status) {
            $data['status'] = $this->status->value;
        }

        return $data;
    }
}
