<?php

namespace Modules\Operation\DTOs;

use App\Contracts\DtoContract;
use Illuminate\Support\Collection;

readonly class CourseDto implements DtoContract
{
    /**
     * @param  Collection<InstitutionDto>|null  $institutions
     */
    public function __construct(
        public string $name,
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

            return new CourseDto(
                name: $course['name'],
                institutions: $institutions,
            );
        }, $data);

        return new Collection($dtos);
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
        ];
    }
}
