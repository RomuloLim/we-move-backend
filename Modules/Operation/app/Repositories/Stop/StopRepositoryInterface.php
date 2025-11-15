<?php

namespace Modules\Operation\Repositories\Stop;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Operation\DTOs\CourseDto;
use Modules\Operation\Models\Course;

interface StopRepositoryInterface
{
    public function all(): array;
    public function paginate(int $perPage = 15): LengthAwarePaginator;
}
