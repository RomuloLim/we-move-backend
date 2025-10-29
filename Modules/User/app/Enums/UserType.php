<?php

namespace Modules\User\Enums;

enum UserType: string
{
    case SuperAdmin = 'super-admin';
    case Admin = 'admin';
    case Student = 'student';
    case Driver = 'driver';

    /**
     * Retorna todos os tipos de usuário que podem ser criados via registro público.
     */
    public static function publicRegistrationTypes(): array
    {
        return [self::Student];
    }

    /**
     * Retorna os tipos que podem ser criados apenas por admins.
     */
    public static function adminOnlyTypes(): array
    {
        return [self::Admin, self::Driver];
    }

    /**
     * Verifica se o tipo pode ser criado publicamente.
     */
    public function canBeCreatedPublicly(): bool
    {
        return in_array($this, self::publicRegistrationTypes());
    }

    /**
     * Verifica se o tipo pode criar outros usuários admin/driver.
     */
    public function canCreateAdminUsers(): bool
    {
        return in_array($this, [self::SuperAdmin, self::Admin]);
    }

    /**
     * Retorna uma descrição amigável do tipo.
     */
    public function label(): string
    {
        return match ($this) {
            self::SuperAdmin => 'Super Administrador',
            self::Admin => 'Administrador',
            self::Student => 'Estudante',
            self::Driver => 'Motorista',
        };
    }
}
