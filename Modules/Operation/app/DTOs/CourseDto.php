<?php

namespace Modules\Operation\DTOs;

class CourseDto
{
    public function __construct(
        public readonly string $name,
    ) {}

    public function toArray(): array
    {
        return [
            'name' => $this->name,
        ];
    }
}
