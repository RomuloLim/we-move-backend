<?php

namespace Modules\Communication\Repositories\Notice;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Communication\DTOs\NoticeDto;
use Modules\Communication\Models\Notice;

class NoticeRepository implements NoticeRepositoryInterface
{
    public function list(?array $routeIds = null, int $perPage = 5): LengthAwarePaginator
    {
        $query = Notice::query()
            ->with(['author', 'route'])
            ->latest();

        if ($routeIds !== null && count($routeIds) > 0) {
            $query->whereIn('route_id', $routeIds);
        }

        return $query->paginate($perPage);
    }

    public function getUnreadForUser(int $userId, int $perPage = 5): LengthAwarePaginator
    {
        return Notice::query()
            ->with(['author', 'route'])
            ->whereDoesntHave('readByUsers', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->oldest()
            ->paginate($perPage);
    }

    public function find(int $id): ?Notice
    {
        return Notice::query()->find($id);
    }

    public function findOrFail(int $id): Notice
    {
        return Notice::query()->findOrFail($id);
    }

    public function create(NoticeDto $data): Notice
    {
        return Notice::query()
            ->create($data->toArray());
    }

    public function update(Notice $notice, NoticeDto $data): Notice
    {
        $notice->update($data->toArray());

        return $notice->fresh();
    }

    public function delete(int $id): bool
    {
        $notice = $this->find($id);

        return (bool) $notice?->delete();
    }
}
