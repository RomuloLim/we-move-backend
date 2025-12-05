<?php

namespace Modules\Communication\Services;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Modules\Communication\DTOs\NoticeDto;
use Modules\Communication\Enums\NoticeType;
use Modules\Communication\Events\NoticeCreated;
use Modules\Communication\Models\Notice;
use Modules\Communication\Repositories\Notice\NoticeRepositoryInterface;

class NoticeService implements NoticeServiceInterface
{
    public function __construct(
        protected NoticeRepositoryInterface $noticeRepository
    ) {}

    public function list(?array $routeIds = null, int $perPage = 5): LengthAwarePaginator
    {
        return $this->noticeRepository->list($routeIds, $perPage);
    }

    public function getUnreadForUser(int $userId, int $perPage = 5): LengthAwarePaginator
    {
        return $this->noticeRepository->getUnreadForUser($userId, $perPage);
    }

    public function find(int $id): ?Notice
    {
        return $this->noticeRepository->find($id);
    }

    public function findOrFail(int $id): Notice
    {
        return $this->noticeRepository->findOrFail($id);
    }

    public function createNotices(int $authorUserId, array $data): array
    {
        return DB::transaction(function () use ($authorUserId, $data): array {
            $type = NoticeType::from($data['type']);
            $notices = [];

            // Se for um aviso geral, criar apenas uma notificação
            if ($type === NoticeType::General) {
                $noticeDto = new NoticeDto(
                    author_user_id: $authorUserId,
                    title: $data['title'],
                    content: $data['content'],
                    type: $type,
                    route_id: null
                );

                $notice = $this->noticeRepository->create($noticeDto);
                NoticeCreated::dispatch($notice);
                $notices[] = $notice;
            }

            // Se for alerta de rota, criar uma notificação para cada rota
            if ($type === NoticeType::RouteAlert && !empty($data['route_ids'])) {
                foreach ($data['route_ids'] as $routeId) {
                    $noticeDto = new NoticeDto(
                        author_user_id: $authorUserId,
                        title: $data['title'],
                        content: $data['content'],
                        type: $type,
                        route_id: $routeId
                    );

                    $notice = $this->noticeRepository->create($noticeDto);
                    NoticeCreated::dispatch($notice);
                    $notices[] = $notice;
                }
            }

            return $notices;
        });
    }

    public function delete(int $id): bool
    {
        return $this->noticeRepository->delete($id);
    }

    public function markAsRead(int $noticeId, int $userId): void
    {
        $notice = $this->noticeRepository->find($noticeId);

        if (!$notice) {
            throw new \Exception('Aviso não encontrado.');
        }

        $this->noticeRepository->markAsRead($noticeId, $userId);
    }
}
