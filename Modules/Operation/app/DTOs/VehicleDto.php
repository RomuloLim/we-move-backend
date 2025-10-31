<?php

namespace Modules\Operation\DTOs;

class VehicleDto
{
    public function __construct(
        public readonly string $license_plate,
        public readonly string $model,
        public readonly int $capacity,
    ) {}

    public function toArray(): array
    {
        return [
            'license_plate' => $this->license_plate,
            'model' => $this->model,
            'capacity' => $this->capacity,
        ];
    }
}
