<?php

namespace Modules\Logistics\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Modules\Logistics\DTOs\UserRouteDto;
use Modules\Logistics\Repositories\UserRoute\UserRouteRepositoryInterface;

class UserRouteService implements UserRouteServiceInterface
{
    public function __construct(protected UserRouteRepositoryInterface $repository) {}

    public function getRoutesByUserId(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->getRoutesByUserId($userId, $perPage);
    }

    public function getAllRoutesOrderedByUser(int $userId, int $perPage = 15): Paginator
    {
        return $this->repository->getAllRoutesOrderedByUser($userId, $perPage);
    }

    public function linkRoutesToUser(UserRouteDto $data): bool
    {
        return $this->repository->linkRoutesToUser($data);
    }

    public function unlinkRoutesFromUser(UserRouteDto $data): bool
    {
        return $this->repository->unlinkRoutesFromUser($data);
    }
}
