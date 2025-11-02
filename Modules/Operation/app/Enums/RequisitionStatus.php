<?php

namespace Modules\Operation\Enums;

enum RequisitionStatus: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Reproved = 'reproved';
    case Expired = 'expired';

    /**
     * Get a human-readable label for the status.
     */
    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pendente',
            self::Approved => 'Aprovado',
            self::Reproved => 'Reprovado',
            self::Expired => 'Expirado',
        };
    }
}
