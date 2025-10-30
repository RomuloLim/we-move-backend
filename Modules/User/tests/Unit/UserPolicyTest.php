<?php

namespace Modules\User\Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\User\Enums\UserType;
use Modules\User\Models\User;
use Tests\TestCase;

class UserPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_view_any_users(): void
    {
        $superAdmin = User::factory()->create(['user_type' => UserType::SuperAdmin]);

        $this->assertTrue($superAdmin->can('viewAny', User::class));
    }

    public function test_admin_can_view_any_users(): void
    {
        $admin = User::factory()->create(['user_type' => UserType::Admin]);

        $this->assertTrue($admin->can('viewAny', User::class));
    }

    public function test_student_cannot_view_any_users(): void
    {
        $student = User::factory()->create(['user_type' => UserType::Student]);

        $this->assertFalse($student->can('viewAny', User::class));
    }

    public function test_driver_cannot_view_any_users(): void
    {
        $driver = User::factory()->create(['user_type' => UserType::Driver]);

        $this->assertFalse($driver->can('viewAny', User::class));
    }

    public function test_user_can_view_own_profile(): void
    {
        $student = User::factory()->create(['user_type' => UserType::Student]);

        $this->assertTrue($student->can('view', $student));
    }

    public function test_admin_can_view_other_user(): void
    {
        $admin = User::factory()->create(['user_type' => UserType::Admin]);
        $student = User::factory()->create(['user_type' => UserType::Student]);

        $this->assertTrue($admin->can('view', $student));
    }

    public function test_student_cannot_view_other_user(): void
    {
        $student1 = User::factory()->create(['user_type' => UserType::Student]);
        $student2 = User::factory()->create(['user_type' => UserType::Student]);

        $this->assertFalse($student1->can('view', $student2));
    }

    public function test_super_admin_can_create_users(): void
    {
        $superAdmin = User::factory()->create(['user_type' => UserType::SuperAdmin]);

        $this->assertTrue($superAdmin->can('create', User::class));
    }

    public function test_admin_can_create_users(): void
    {
        $admin = User::factory()->create(['user_type' => UserType::Admin]);

        $this->assertTrue($admin->can('create', User::class));
    }

    public function test_student_cannot_create_users(): void
    {
        $student = User::factory()->create(['user_type' => UserType::Student]);

        $this->assertFalse($student->can('create', User::class));
    }

    public function test_super_admin_can_create_any_user_type(): void
    {
        $superAdmin = User::factory()->create(['user_type' => UserType::SuperAdmin]);

        $this->assertTrue($superAdmin->can('createUserType', [User::class, UserType::Student]));
        $this->assertTrue($superAdmin->can('createUserType', [User::class, UserType::Driver]));
        $this->assertTrue($superAdmin->can('createUserType', [User::class, UserType::Admin]));
        $this->assertTrue($superAdmin->can('createUserType', [User::class, UserType::SuperAdmin]));
    }

    public function test_admin_cannot_create_super_admin(): void
    {
        $admin = User::factory()->create(['user_type' => UserType::Admin]);

        $this->assertFalse($admin->can('createUserType', [User::class, UserType::SuperAdmin]));
    }

    public function test_admin_can_create_other_user_types(): void
    {
        $admin = User::factory()->create(['user_type' => UserType::Admin]);

        $this->assertTrue($admin->can('createUserType', [User::class, UserType::Student]));
        $this->assertTrue($admin->can('createUserType', [User::class, UserType::Driver]));
        $this->assertTrue($admin->can('createUserType', [User::class, UserType::Admin]));
    }

    public function test_user_can_update_own_profile(): void
    {
        $student = User::factory()->create(['user_type' => UserType::Student]);

        $this->assertTrue($student->can('update', $student));
    }

    public function test_admin_can_update_other_user(): void
    {
        $admin = User::factory()->create(['user_type' => UserType::Admin]);
        $student = User::factory()->create(['user_type' => UserType::Student]);

        $this->assertTrue($admin->can('update', $student));
    }

    public function test_student_cannot_update_other_user(): void
    {
        $student1 = User::factory()->create(['user_type' => UserType::Student]);
        $student2 = User::factory()->create(['user_type' => UserType::Student]);

        $this->assertFalse($student1->can('update', $student2));
    }

    public function test_super_admin_can_update_user_type(): void
    {
        $superAdmin = User::factory()->create(['user_type' => UserType::SuperAdmin]);
        $student = User::factory()->create(['user_type' => UserType::Student]);

        $this->assertTrue($superAdmin->can('updateUserType', [$student, UserType::Driver]));
        $this->assertTrue($superAdmin->can('updateUserType', [$student, UserType::Admin]));
    }

    public function test_admin_can_update_user_type(): void
    {
        $admin = User::factory()->create(['user_type' => UserType::Admin]);
        $student = User::factory()->create(['user_type' => UserType::Student]);

        $this->assertTrue($admin->can('updateUserType', [$student, UserType::Driver]));
    }

    public function test_admin_cannot_promote_to_super_admin(): void
    {
        $admin = User::factory()->create(['user_type' => UserType::Admin]);
        $student = User::factory()->create(['user_type' => UserType::Student]);

        $this->assertFalse($admin->can('updateUserType', [$student, UserType::SuperAdmin]));
    }

    public function test_user_cannot_change_own_type(): void
    {
        $admin = User::factory()->create(['user_type' => UserType::Admin]);

        $this->assertFalse($admin->can('updateUserType', [$admin, UserType::SuperAdmin]));
    }

    public function test_cannot_change_super_admin_type(): void
    {
        $superAdmin1 = User::factory()->create(['user_type' => UserType::SuperAdmin]);
        $superAdmin2 = User::factory()->create(['user_type' => UserType::SuperAdmin]);

        $this->assertFalse($superAdmin1->can('updateUserType', [$superAdmin2, UserType::Admin]));
    }

    public function test_admin_can_delete_user(): void
    {
        $admin = User::factory()->create(['user_type' => UserType::Admin]);
        $student = User::factory()->create(['user_type' => UserType::Student]);

        $this->assertTrue($admin->can('delete', $student));
    }

    public function test_user_cannot_delete_self(): void
    {
        $admin = User::factory()->create(['user_type' => UserType::Admin]);

        $this->assertFalse($admin->can('delete', $admin));
    }

    public function test_cannot_delete_super_admin(): void
    {
        $admin = User::factory()->create(['user_type' => UserType::Admin]);
        $superAdmin = User::factory()->create(['user_type' => UserType::SuperAdmin]);

        $this->assertFalse($admin->can('delete', $superAdmin));
    }

    public function test_student_cannot_delete_user(): void
    {
        $student1 = User::factory()->create(['user_type' => UserType::Student]);
        $student2 = User::factory()->create(['user_type' => UserType::Student]);

        $this->assertFalse($student1->can('delete', $student2));
    }

    public function test_only_super_admin_can_force_delete(): void
    {
        $superAdmin = User::factory()->create(['user_type' => UserType::SuperAdmin]);
        $admin = User::factory()->create(['user_type' => UserType::Admin]);
        $student = User::factory()->create(['user_type' => UserType::Student]);

        $this->assertTrue($superAdmin->can('forceDelete', $student));
        $this->assertFalse($admin->can('forceDelete', $student));
    }

    public function test_cannot_force_delete_super_admin(): void
    {
        $superAdmin1 = User::factory()->create(['user_type' => UserType::SuperAdmin]);
        $superAdmin2 = User::factory()->create(['user_type' => UserType::SuperAdmin]);

        $this->assertFalse($superAdmin1->can('forceDelete', $superAdmin2));
    }
}
