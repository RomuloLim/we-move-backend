<?php

namespace Modules\Logistics\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Logistics\DTOs\UserRouteDto;

interface UserRouteServiceInterface
{
    public function getRoutesByUserId(int $userId, int $perPage = 15): LengthAwarePaginator;

    public function linkRoutesToUser(UserRouteDto $data): bool;

    public function unlinkRoutesFromUser(UserRouteDto $data): bool;
}
