<?php

namespace Modules\User\Tests\Unit;

use Modules\User\Enums\{Permission, UserType};
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PermissionTest extends TestCase
{
    #[DataProvider('permissionProfileCases')]
    public function test_permission_profile_cases(UserType $userType, array $expectedPermissions): void
    {
        $permissions = Permission::forUserType($userType);
        $this->assertEqualsCanonicalizing($expectedPermissions, $permissions);
    }

    public static function permissionProfileCases(): \Generator
    {
        yield 'Super Admin user type' => [
            UserType::SuperAdmin,
            Permission::superAdminPermissions(),
        ];

        yield 'Admin user type' => [
            UserType::Admin,
            Permission::adminPermissions(),
        ];

        yield 'Driver user type' => [
            UserType::Driver,
            Permission::driverPermissions(),
        ];

        yield 'Student user type' => [
            UserType::Student,
            Permission::studentPermissions(),
        ];
    }

    public function test_student_has_no_permissions(): void
    {
        $permissions = Permission::forUserType(UserType::Student);

        $this->assertEmpty($permissions);
    }

    public function test_permission_labels(): void
    {
        $this->assertEquals('Visualizar usuários', Permission::ViewUsers->label());
        $this->assertEquals('Criar usuários', Permission::CreateUsers->label());
        $this->assertEquals('Atualizar usuários', Permission::UpdateUsers->label());
        $this->assertEquals('Deletar usuários', Permission::DeleteUsers->label());
        $this->assertEquals('Atualizar tipo de usuário', Permission::UpdateUserType->label());
        $this->assertEquals('Criar usuários Admin/Driver', Permission::CreateAdminUsers->label());
        $this->assertEquals('Criar Super Administrador', Permission::CreateSuperAdmin->label());
    }
}
