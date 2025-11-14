<?php

namespace Modules\Operation\Enums;

enum CourseType: string
{
    case Graduate = 'graduate';
    case Postgraduate = 'postgraduate';
    case Extension = 'extension';

    case Technical = 'technical';

    case Other = 'other';

    /**
     * Get a human-readable label for the atuation form.
     */
    public function label(): string
    {
        return match ($this) {
            self::Graduate => 'Graduação',
            self::Postgraduate => 'Pós-Graduação',
            self::Extension => 'Curso de Extensão',
            self::Technical => 'Técnico',
            self::Other => 'Outro',
        };
    }
}
