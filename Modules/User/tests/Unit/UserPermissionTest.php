<?php

namespace Modules\User\Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\User\Enums\Permission;
use Modules\User\Enums\UserType;
use Modules\User\Models\User;
use Tests\TestCase;

class UserPermissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_has_permission(): void
    {
        $user = User::factory()->create(['user_type' => UserType::SuperAdmin]);

        $this->assertTrue($user->hasPermission(Permission::ViewUsers));
        $this->assertTrue($user->hasPermission(Permission::CreateUsers));
        $this->assertTrue($user->hasPermission(Permission::UpdateUsers));
        $this->assertTrue($user->hasPermission(Permission::DeleteUsers));
        $this->assertTrue($user->hasPermission(Permission::UpdateUserType));
        $this->assertTrue($user->hasPermission(Permission::CreateAdminUsers));
        $this->assertTrue($user->hasPermission(Permission::CreateSuperAdmin));
    }

    public function test_admin_has_permission(): void
    {
        $user = User::factory()->create(['user_type' => UserType::Admin]);

        $this->assertTrue($user->hasPermission(Permission::ViewUsers));
        $this->assertTrue($user->hasPermission(Permission::CreateUsers));
        $this->assertTrue($user->hasPermission(Permission::UpdateUsers));
        $this->assertTrue($user->hasPermission(Permission::DeleteUsers));
        $this->assertTrue($user->hasPermission(Permission::UpdateUserType));
        $this->assertTrue($user->hasPermission(Permission::CreateAdminUsers));
        $this->assertFalse($user->hasPermission(Permission::CreateSuperAdmin));
    }

    public function test_student_has_no_permissions(): void
    {
        $user = User::factory()->create(['user_type' => UserType::Student]);

        $this->assertFalse($user->hasPermission(Permission::ViewUsers));
        $this->assertFalse($user->hasPermission(Permission::CreateUsers));
        $this->assertFalse($user->hasPermission(Permission::UpdateUsers));
        $this->assertFalse($user->hasPermission(Permission::DeleteUsers));
    }

    public function test_driver_has_no_permissions(): void
    {
        $user = User::factory()->create(['user_type' => UserType::Driver]);

        $this->assertFalse($user->hasPermission(Permission::ViewUsers));
        $this->assertFalse($user->hasPermission(Permission::CreateUsers));
        $this->assertFalse($user->hasPermission(Permission::UpdateUsers));
        $this->assertFalse($user->hasPermission(Permission::DeleteUsers));
    }

    public function test_has_any_permission(): void
    {
        $admin = User::factory()->create(['user_type' => UserType::Admin]);
        $student = User::factory()->create(['user_type' => UserType::Student]);

        $this->assertTrue($admin->hasAnyPermission([Permission::ViewUsers, Permission::CreateUsers]));
        $this->assertTrue($admin->hasAnyPermission([Permission::CreateSuperAdmin, Permission::ViewUsers]));
        $this->assertFalse($admin->hasAnyPermission([Permission::CreateSuperAdmin]));

        $this->assertFalse($student->hasAnyPermission([Permission::ViewUsers, Permission::CreateUsers]));
    }

    public function test_has_all_permissions(): void
    {
        $admin = User::factory()->create(['user_type' => UserType::Admin]);
        $superAdmin = User::factory()->create(['user_type' => UserType::SuperAdmin]);
        $student = User::factory()->create(['user_type' => UserType::Student]);

        $this->assertTrue($admin->hasAllPermissions([Permission::ViewUsers, Permission::CreateUsers]));
        $this->assertFalse($admin->hasAllPermissions([Permission::ViewUsers, Permission::CreateSuperAdmin]));

        $this->assertTrue($superAdmin->hasAllPermissions([Permission::ViewUsers, Permission::CreateSuperAdmin]));

        $this->assertFalse($student->hasAllPermissions([Permission::ViewUsers]));
    }

    public function test_get_permissions(): void
    {
        $superAdmin = User::factory()->create(['user_type' => UserType::SuperAdmin]);
        $admin = User::factory()->create(['user_type' => UserType::Admin]);
        $student = User::factory()->create(['user_type' => UserType::Student]);

        $this->assertCount(7, $superAdmin->getPermissions());
        $this->assertCount(6, $admin->getPermissions());
        $this->assertCount(0, $student->getPermissions());
    }
}
