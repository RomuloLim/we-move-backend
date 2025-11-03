<?php

namespace Modules\Operation\Enums;

enum AtuationForm: string
{
    case Student = 'student';
    case Bolsist = 'bolsist';
    case Teacher = 'teacher';
    case PrepCourse = 'prep_course';
    case Other = 'other';

    /**
     * Get a human-readable label for the atuation form.
     */
    public function label(): string
    {
        return match ($this) {
            self::Student => 'Estudante',
            self::Bolsist => 'Bolsista',
            self::Teacher => 'Professor',
            self::PrepCourse => 'Cursinho',
            self::Other => 'Outro',
        };
    }
}
