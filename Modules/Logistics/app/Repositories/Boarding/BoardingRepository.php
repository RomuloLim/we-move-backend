<?php

namespace Modules\Logistics\Repositories\Boarding;

use Modules\Logistics\DTOs\BoardingDto;
use Modules\Logistics\Models\Boarding;

class BoardingRepository implements BoardingRepositoryInterface
{
    public function create(BoardingDto $data): Boarding
    {
        return Boarding::create($data->toArray());
    }

    public function findActiveBoarding(int $studentId): ?Boarding
    {
        return Boarding::query()
            ->where('student_id', $studentId)
            ->whereNull('landed_at')
            ->latest('boarding_timestamp')
            ->first();
    }

    public function unboard(Boarding $boarding): Boarding
    {
        $boarding->update([
            'landed_at' => now(),
        ]);

        return $boarding->fresh();
    }

    public function unboardAllByTripId(int $tripId): int
    {
        return Boarding::query()
            ->where('trip_id', $tripId)
            ->whereNull('landed_at')
            ->update([
                'landed_at' => now(),
            ]);
    }

    public function getPassengersByTripId(int $tripId, ?bool $onlyBoarded = null): \Illuminate\Database\Eloquent\Collection
    {
        $query = Boarding::query()
            ->where('trip_id', $tripId)
            ->with(['student', 'stop']);

        if ($onlyBoarded === true) {
            $query->whereNull('landed_at');
        } elseif ($onlyBoarded === false) {
            $query->whereNotNull('landed_at');
        }

        return $query->orderBy('boarding_timestamp', 'desc')->get();
    }
}
