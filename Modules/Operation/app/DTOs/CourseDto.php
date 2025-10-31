<?php

namespace Modules\Operation\DTOs;

use Nwidart\Modules\Collection;

class CourseDto
{
    public function __construct(
        public readonly string $name,
    ) {}

    public static function collection(array $courses): Collection
    {
        $dtos = array_map(fn($course) => new self(
            name: $course['name'],
        ), $courses);

        return new Collection($dtos);
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
        ];
    }
}
