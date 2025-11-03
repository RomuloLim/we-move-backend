<?php

namespace Modules\Operation\DTOs;

use DateTimeInterface;
use Modules\Operation\Enums\DocumentType;

readonly class DocumentDto
{
    public function __construct(
        public int $student_id,
        public DocumentType $type,
        public string $file_url,
        public ?DateTimeInterface $uploaded_at = null,
    ) {}

    public function toArray(): array
    {
        return [
            'student_id' => $this->student_id,
            'type' => $this->type,
            'file_url' => $this->file_url,
            'uploaded_at' => $this->uploaded_at ?? now(),
        ];
    }
}
