<?php

namespace Modules\User\Tests\Unit;

use Modules\User\Enums\{Permission, UserType};
use Tests\TestCase;

class PermissionTest extends TestCase
{
    public function test_permission_enum_values(): void
    {
        $permissions = [
            'view-users',
            'create-users',
            'update-users',
            'delete-users',
            'update-user-type',
            'create-admin-users',
            'create-super-admin',
        ];

        $enumValues = array_map(fn ($p) => $p->value, Permission::cases());

        $this->assertEquals($permissions, $enumValues);
    }

    public function test_super_admin_has_all_permissions(): void
    {
        $permissions = Permission::forUserType(UserType::SuperAdmin);

        $this->assertCount(7, $permissions);
        $this->assertContains(Permission::ViewUsers, $permissions);
        $this->assertContains(Permission::CreateUsers, $permissions);
        $this->assertContains(Permission::UpdateUsers, $permissions);
        $this->assertContains(Permission::DeleteUsers, $permissions);
        $this->assertContains(Permission::UpdateUserType, $permissions);
        $this->assertContains(Permission::CreateAdminUsers, $permissions);
        $this->assertContains(Permission::CreateSuperAdmin, $permissions);
    }

    public function test_admin_has_management_permissions_except_super_admin(): void
    {
        $permissions = Permission::forUserType(UserType::Admin);

        $this->assertCount(6, $permissions);
        $this->assertContains(Permission::ViewUsers, $permissions);
        $this->assertContains(Permission::CreateUsers, $permissions);
        $this->assertContains(Permission::UpdateUsers, $permissions);
        $this->assertContains(Permission::DeleteUsers, $permissions);
        $this->assertContains(Permission::UpdateUserType, $permissions);
        $this->assertContains(Permission::CreateAdminUsers, $permissions);
        $this->assertNotContains(Permission::CreateSuperAdmin, $permissions);
    }

    public function test_driver_has_no_permissions(): void
    {
        $permissions = Permission::forUserType(UserType::Driver);

        $this->assertEmpty($permissions);
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
