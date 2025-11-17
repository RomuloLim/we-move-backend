<?php

namespace Modules\Operation\DTOs;

use App\Contracts\DtoContract;
use Illuminate\Support\Collection;

readonly class StopDto implements DtoContract
{
    public function __construct(
        public int $routeId,
        public string $stopName,
        public ?float $latitude = null,
        public ?float $longitude = null,
        public ?string $scheduledTime = null,
        public ?int $order = null,
    ) {}

    public static function collection(array $data): Collection
    {
        $dtos = array_map(function ($stop) {
            return new StopDto(
                routeId: $stop['route_id'],
                stopName: $stop['stop_name'],
                latitude: $stop['latitude'] ?? null,
                longitude: $stop['longitude'] ?? null,
                scheduledTime: $stop['scheduled_time'] ?? null,
                order: $stop['order'] ?? null,
            );
        }, $data);

        return new Collection($dtos);
    }

    public function toArray(): array
    {
        return [
            'route_id' => $this->routeId,
            'stop_name' => $this->stopName,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'scheduled_time' => $this->scheduledTime,
            'order' => $this->order,
        ];
    }
}
