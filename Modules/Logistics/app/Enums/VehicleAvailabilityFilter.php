<?php

namespace Modules\Logistics\Enums;

enum VehicleAvailabilityFilter: string
{
    case All = 'all';
    case Available = 'available';
    case InUse = 'in_use';

    /**
     * Display label for the filter.
     */
    public function label(): string
    {
        return match ($this) {
            self::All => 'Todos',
            self::Available => 'Disponíveis',
            self::InUse => 'Em Uso',
        };
    }
}
