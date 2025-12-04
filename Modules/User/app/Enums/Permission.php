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

    // Vehicle Permissions
    case ViewVehicles = 'view-vehicles';
    case ManageVehicles = 'manage-vehicles';

    // Institution and Course Permissions
    case ViewInstitutions = 'view-institutions';
    case ManageInstitutions = 'manage-institutions';
    case ViewCourses = 'view-courses';
    case ManageCourses = 'manage-courses';

    // Routes and Stops Permissions
    case ViewRoutes = 'view-routes';
    case ManageRoutes = 'manage-routes';
    case ViewStops = 'view-stops';
    case ManageStops = 'manage-stops';

    // Trip Permissions
    case ViewTrips = 'view-trips';
    case ManageTrips = 'manage-trips';

    // Student Requisition Permissions
    case SubmitRequisition = 'submit-requisition';
    case ViewRequisitions = 'view-requisitions';
    case ManageRequisitions = 'manage-requisitions';

    /**
     * Get all permissions for a given user type.
     */
    public static function forUserType(UserType $userType): array
    {
        return match ($userType) {
            UserType::SuperAdmin => self::superAdminPermissions(),
            UserType::Admin => self::adminPermissions(),
            UserType::Driver => self::driverPermissions(),
            UserType::Student => self::studentPermissions(),
        };
    }

    public static function superAdminPermissions(): array
    {
        return self::cases();
    }

    public static function adminPermissions(): array
    {
        return [
            self::ViewUsers,
            self::CreateUsers,
            self::UpdateUsers,
            self::DeleteUsers,
            self::UpdateUserType,
            self::CreateAdminUsers,
            self::ViewVehicles,
            self::ManageVehicles,
            self::ViewInstitutions,
            self::ManageInstitutions,
            self::ViewCourses,
            self::ManageCourses,
            self::ViewRoutes,
            self::ManageRoutes,
            self::ViewStops,
            self::ManageStops,
            self::ViewTrips,
            self::ManageTrips,
            self::ViewRequisitions,
            self::ManageRequisitions,
        ];
    }

    public static function driverPermissions(): array
    {
        return [
            self::ViewVehicles,
            self::ViewRoutes,
            self::ViewStops,
            self::ViewTrips,
            self::ManageTrips,
        ];
    }

    public static function studentPermissions(): array
    {
        return [
            self::ViewInstitutions,
            self::ViewCourses,
            self::SubmitRequisition,
            self::ViewRoutes
        ];
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
            self::ViewVehicles => 'Visualizar veículos',
            self::ManageVehicles => 'Gerenciar veículos',
            self::ViewInstitutions => 'Visualizar instituições',
            self::ManageInstitutions => 'Gerenciar instituições',
            self::ViewCourses => 'Visualizar cursos',
            self::ManageCourses => 'Gerenciar cursos',
            self::ViewRoutes => 'Visualizar rotas',
            self::ManageRoutes => 'Gerenciar rotas',
            self::ViewStops => 'Visualizar paradas',
            self::ManageStops => 'Gerenciar paradas',
            self::ViewTrips => 'Visualizar viagens',
            self::ManageTrips => 'Gerenciar viagens',
            self::SubmitRequisition => 'Enviar solicitação',
            self::ViewRequisitions => 'Visualizar solicitações',
            self::ManageRequisitions => 'Gerenciar solicitações',
        };
    }
}
