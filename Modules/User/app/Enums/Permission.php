<?php

namespace Modules\User\Enums;

/**
 * Define all permissions available in the system.
 * This enum is used to define what actions users can perform.
 */
enum Permission: string
{
    // User Management Permissions
    case ViewUsers = 'view-users';
    case CreateUsers = 'create-users';
    case UpdateUsers = 'update-users';
    case DeleteUsers = 'delete-users';
    case UpdateUserType = 'update-user-type';
    case CreateAdminUsers = 'create-admin-users';
    case CreateSuperAdmin = 'create-super-admin';

    /**
     * Get all permissions for a given user type.
     */
    public static function forUserType(UserType $userType): array
    {
        return match ($userType) {
            UserType::SuperAdmin => [
                self::ViewUsers,
                self::CreateUsers,
                self::UpdateUsers,
                self::DeleteUsers,
                self::UpdateUserType,
                self::CreateAdminUsers,
                self::CreateSuperAdmin,
            ],
            UserType::Admin => [
                self::ViewUsers,
                self::CreateUsers,
                self::UpdateUsers,
                self::DeleteUsers,
                self::UpdateUserType,
                self::CreateAdminUsers,
            ],
            UserType::Driver => [],
            UserType::Student => [],
        };
    }

    /**
     * Get a human-readable label for the permission.
     */
    public function label(): string
    {
        return match ($this) {
            self::ViewUsers => 'Visualizar usuários',
            self::CreateUsers => 'Criar usuários',
            self::UpdateUsers => 'Atualizar usuários',
            self::DeleteUsers => 'Deletar usuários',
            self::UpdateUserType => 'Atualizar tipo de usuário',
            self::CreateAdminUsers => 'Criar usuários Admin/Driver',
            self::CreateSuperAdmin => 'Criar Super Administrador',
        };
    }
}
