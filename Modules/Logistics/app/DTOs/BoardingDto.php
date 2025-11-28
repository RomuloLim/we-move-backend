<?php

namespace Modules\Logistics\DTOs;

use App\Contracts\DtoContract;
use Illuminate\Support\Collection;

readonly class BoardingDto implements DtoContract
{
    public function __construct(
        public int $tripId,
        public int $studentId,
        public int $stopId,
        public string $qrcodeToken,
        public int $driverId,
    ) {}

    public static function collection(array $data): Collection
    {
        $dtos = array_map(function ($boarding) {
            return new BoardingDto(
                tripId: $boarding['trip_id'],
                studentId: $boarding['student_id'],
                stopId: $boarding['stop_id'],
                qrcodeToken: $boarding['qrcode_token'],
                driverId: $boarding['driver_id'],
            );
        }, $data);

        return new Collection($dtos);
    }

    public function toArray(): array
    {
        return [
            'trip_id' => $this->tripId,
            'student_id' => $this->studentId,
            'stop_id' => $this->stopId,
        ];
    }
}
