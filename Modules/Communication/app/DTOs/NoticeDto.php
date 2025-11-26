<?php

namespace Modules\Communication\DTOs;

use Modules\Communication\Enums\NoticeType;

class NoticeDto
{
    public function __construct(
        public int $author_user_id,
        public string $title,
        public string $content,
        public NoticeType $type,
        public ?int $route_id = null,
    ) {}

    public function toArray(): array
    {
        return array_filter([
            'author_user_id' => $this->author_user_id,
            'title' => $this->title,
            'content' => $this->content,
            'type' => $this->type,
            'route_id' => $this->route_id,
        ], fn ($value): bool => $value !== null);
    }
}
