<?php

namespace Modules\Communication\Services;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Communication\Models\Notice;

interface NoticeServiceInterface
{
    /**
     * List notices with optional route filter and pagination.
     *
     * @param  array<int>|null  $routeIds
     */
    public function list(?array $routeIds = null, int $perPage = 5): LengthAwarePaginator;

    /**
     * Find a notice by ID.
     */
    public function find(int $id): ?Notice;

    /**
     * Find a notice by ID or fail.
     */
    public function findOrFail(int $id): Notice;

    /**
     * Create notices for multiple routes or a single general notice.
     *
     * @param  array{title: string, content: string, type: string, route_ids: array<int>|null}  $data
     * @return array<Notice>
     */
    public function createNotices(int $authorUserId, array $data): array;

    /**
     * Delete a notice by ID.
     */
    public function delete(int $id): bool;
}
