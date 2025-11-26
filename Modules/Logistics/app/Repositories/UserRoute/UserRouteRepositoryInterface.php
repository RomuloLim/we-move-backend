<?php

namespace Modules\Logistics\Repositories\UserRoute;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Modules\Logistics\DTOs\UserRouteDto;

interface UserRouteRepositoryInterface
{
    public function getRoutesByUserId(int $userId, int $perPage = 15): LengthAwarePaginator;

    public function getAllRoutesOrderedByUser(int $userId, int $perPage = 15): Paginator;

    public function linkRoutesToUser(UserRouteDto $data): bool;

    public function unlinkRoutesFromUser(UserRouteDto $data): bool;
}
