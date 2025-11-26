<?php

namespace Modules\Communication\Repositories\Notice;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Communication\DTOs\NoticeDto;
use Modules\Communication\Models\Notice;

interface NoticeRepositoryInterface
{
    /**
     * List notices with optional route filter and pagination.
     *
     * @param  array<int>|null  $routeIds
     */
    public function list(?array $routeIds = null, int $perPage = 5): LengthAwarePaginator;

    /**
     * Get unread notices for a specific user, ordered from oldest to newest.
     */
    public function getUnreadForUser(int $userId, int $perPage = 5): LengthAwarePaginator;

    /**
     * Find a notice by ID.
     */
    public function find(int $id): ?Notice;

    /**
     * Find a notice by ID or fail.
     */
    public function findOrFail(int $id): Notice;

    /**
     * Create a new notice.
     */
    public function create(NoticeDto $data): Notice;

    /**
     * Update a notice.
     */
    public function update(Notice $notice, NoticeDto $data): Notice;

    /**
     * Delete a notice by ID.
     */
    public function delete(int $id): bool;
}
