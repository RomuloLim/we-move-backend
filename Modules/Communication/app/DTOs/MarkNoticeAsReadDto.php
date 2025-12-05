<?php

namespace Modules\Communication\DTOs;

use App\Contracts\DtoContract;
use Illuminate\Support\Collection;

readonly class MarkNoticeAsReadDto implements DtoContract
{
    public function __construct(
        public int $noticeId,
        public int $userId,
    ) {}

    public function toArray(): array
    {
        return [
            'notice_id' => $this->noticeId,
            'user_id' => $this->userId,
        ];
    }

    public static function collection(array $data): Collection
    {
        return collect($data)->map(fn (array $item) => new self(
            noticeId: $item['notice_id'],
            userId: $item['user_id'],
        ));
    }
}
