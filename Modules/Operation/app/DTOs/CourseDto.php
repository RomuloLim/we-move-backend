<?php

namespace Modules\Operation\DTOs;

use App\Contracts\DtoContract;
use Illuminate\Support\Collection;
use Modules\Operation\Enums\CourseType;

readonly class CourseDto implements DtoContract
{
    /**
     * @param  Collection<InstitutionDto>|null  $institutions
     */
    public function __construct(
        public string $name,
        public CourseType $courseType,
        public ?string $description = null,
        public ?Collection $institutions = null,
    ) {}

    public static function collection(array $data): Collection
    {
        $dtos = array_map(function ($course) {
            $hasInstitutions = array_key_exists('institutions', $course);
            $institutions = null;

            if ($hasInstitutions && isset($course['institutions'])) {
                $institutions = InstitutionDto::collection($course['institutions']);
            }

            $course['course_type'] = $course['course_type'] instanceof CourseType
                ? $course['course_type']
                : CourseType::from($course['course_type']);

            return new CourseDto(
                name: $course['name'],
                courseType: $course['course_type'],
                description: $course['description'] ?? null,
                institutions: $institutions,
            );
        }, $data);

        return new Collection($dtos);
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'course_type' => $this->courseType->value,
            'description' => $this->description,
        ];
    }
}
