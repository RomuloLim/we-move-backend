<?php

namespace Modules\Logistics\Repositories\UserRoute;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Modules\Logistics\DTOs\UserRouteDto;
use Modules\Logistics\Models\Route;

class UserRouteRepository implements UserRouteRepositoryInterface
{
    public function getRoutesByUserId(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return Route::query()
            ->join('user_routes', 'routes.id', '=', 'user_routes.route_id')
            ->where('user_routes.user_id', $userId)
            ->select('routes.*', 'user_routes.created_at as linked_at')
            ->paginate($perPage);
    }

    public function getAllRoutesOrderedByUser(int $userId, int $perPage = 15): Paginator
    {
        return Route::query()
            ->selectRaw('routes.*, user_routes.user_id IS NOT NULL as is_linked')
            ->leftJoin('user_routes', function ($join) use ($userId) {
                $join->on('routes.id', '=', 'user_routes.route_id')
                    ->where('user_routes.user_id', '=', $userId);
            })
            ->orderByRaw('CASE WHEN user_routes.user_id IS NOT NULL THEN 0 ELSE 1 END')
            ->orderBy('routes.route_name')
            ->simplePaginate($perPage);
    }

    public function linkRoutesToUser(UserRouteDto $data): bool
    {
        $records = collect($data->routeIds)->map(function ($routeId) use ($data) {
            return [
                'user_id' => $data->userId,
                'route_id' => $routeId,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        })->toArray();

        return DB::table('user_routes')->insertOrIgnore($records) !== false;
    }

    public function unlinkRoutesFromUser(UserRouteDto $data): bool
    {
        return DB::table('user_routes')
            ->where('user_id', $data->userId)
            ->whereIn('route_id', $data->routeIds)
            ->delete() !== false;
    }
}
