<?php

namespace Modules\Logistics\DTOs;

use App\Contracts\DtoContract;
use Illuminate\Support\Collection;

readonly class UserRouteDto implements DtoContract
{
    public function __construct(
        public int $userId,
        public array $routeIds,
    ) {}

    public static function collection(array $data): Collection
    {
        $dtos = array_map(function ($item) {
            return new UserRouteDto(
                userId: $item['user_id'],
                routeIds: $item['route_ids'],
            );
        }, $data);

        return new Collection($dtos);
    }

    public function toArray(): array
    {
        return [
            'user_id' => $this->userId,
            'route_ids' => $this->routeIds,
        ];
    }
}
