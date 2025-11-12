<?php

namespace Modules\User\DTOs;

use Modules\User\Enums\UserType;

readonly class SearchParamsDTO
{
    public function __construct(
        public ?string $search = null,
        public ?UserType $type = null,
        public ?int $perPage = 15,
    ) {}
}
