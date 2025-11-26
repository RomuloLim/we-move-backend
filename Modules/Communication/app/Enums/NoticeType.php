<?php

namespace Modules\Communication\Enums;

enum NoticeType: string
{
    case General = 'general';
    case RouteAlert = 'route_alert';

    /**
     * Get a human-readable label for the notice type.
     */
    public function label(): string
    {
        return match ($this) {
            self::General => 'Geral',
            self::RouteAlert => 'Alerta de Rota',
        };
    }
}
