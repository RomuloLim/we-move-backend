<?php

namespace Modules\Logistics\Enums;

enum TripStatus: string
{
    case Scheduled = 'Scheduled';
    case InProgress = 'In_Progress';
    case Completed = 'Completed';
    case Canceled = 'Canceled';

    /**
     * Display label for the status.
     */
    public function label(): string
    {
        return match ($this) {
            self::Scheduled => 'Agendado',
            self::InProgress => 'Em Progresso',
            self::Completed => 'Completado',
            self::Canceled => 'Cancelado',
        };
    }

    /**
     * Checks if the status can be changed to another status.
     */
    public function canTransitionTo(TripStatus $newStatus): bool
    {
        return match ($this) {
            self::Scheduled => in_array($newStatus, [self::InProgress, self::Canceled]),
            self::InProgress => in_array($newStatus, [self::Completed, self::Canceled]),
            self::Completed => false,
            self::Canceled => false,
        };
    }

    /**
     * Checks if the trip is active (in progress).
     */
    public function isActive(): bool
    {
        return $this === self::InProgress;
    }

    /**
     * Checks if the trip is finished (completed or canceled).
     */
    public function isFinished(): bool
    {
        return in_array($this, [self::Completed, self::Canceled]);
    }
}
