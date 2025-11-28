<?php

namespace Modules\Logistics\Services;

use Modules\Logistics\DTOs\BoardingDto;
use Modules\Logistics\Models\Boarding;

interface BoardingServiceInterface
{
    public function boardStudent(BoardingDto $data): Boarding;

    public function unboardStudent(int $tripId, int $studentId, int $requesterId): Boarding;

    public function unboardAllStudents(int $tripId): int;
}
