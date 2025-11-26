<?php

namespace Modules\Logistics\DTOs;

use App\Contracts\DtoContract;
use Illuminate\Support\Collection;

readonly class RouteDto implements DtoContract
{
    /**
     * @param  Collection<StopDto>|null  $stops
     */
    public function __construct(
        public string $routeName,
        public ?string $description = null,
        public ?Collection $stops = null,
    ) {}

    public static function collection(array $data): Collection
    {
        $dtos = array_map(function ($route) {
            $hasStops = array_key_exists('stops', $route);
            $stops = null;

            if ($hasStops && isset($route['stops'])) {
                $stops = StopDto::collection($route['stops']);
            }

            return new RouteDto(
                routeName: $route['route_name'],
                description: $route['description'] ?? null,
                stops: $stops,
            );
        }, $data);

        return new Collection($dtos);
    }

    public function toArray(): array
    {
        return [
            'route_name' => $this->routeName,
            'description' => $this->description,
        ];
    }
}
