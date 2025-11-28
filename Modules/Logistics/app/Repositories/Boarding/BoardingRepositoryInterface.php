<?php

namespace Modules\Logistics\Repositories\Boarding;

use Modules\Logistics\DTOs\BoardingDto;
use Modules\Logistics\Models\Boarding;

interface BoardingRepositoryInterface
{
    public function create(BoardingDto $data): Boarding;

    public function findActiveBoarding(int $studentId): ?Boarding;

    public function unboard(Boarding $boarding): Boarding;

    public function unboardAllByTripId(int $tripId): int;
}
