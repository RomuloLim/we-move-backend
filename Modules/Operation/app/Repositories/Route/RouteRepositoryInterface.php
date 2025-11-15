<?php

namespace Modules\Operation\Repositories\Route;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Operation\DTOs\CourseDto;
use Modules\Operation\Models\Course;

interface RouteRepositoryInterface
{
    public function all(): array;
    public function paginate(int $perPage = 15): LengthAwarePaginator;
}
