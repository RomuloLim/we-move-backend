<?php

namespace Modules\User\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Modules\User\Enums\{Permission, UserType};
use Modules\User\Models\User;
use Tests\TestCase;

/**
 * Integration tests for ACL system.
 * Tests the complete flow of permission checking from routes to policies.
 */
class AclIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_policy_enforces_view_any_permission(): void
    {
        $admin = User::factory()->create(['user_type' => UserType::Admin]);
        $student = User::factory()->create(['user_type' => UserType::Student]);

        $this->assertTrue($admin->can('viewAny', User::class));
        $this->assertFalse($student->can('viewAny', User::class));
    }

    public function test_policy_allows_viewing_own_profile(): void
    {
        $student = User::factory()->create(['user_type' => UserType::Student]);

        // Student can view their own profile
        $this->assertTrue($student->can('view', $student));

        // But not other students
        $otherStudent = User::factory()->create(['user_type' => UserType::Student]);
        $this->assertFalse($student->can('view', $otherStudent));
    }

    public function test_policy_enforces_create_user_type_restrictions(): void
    {
        $superAdmin = User::factory()->create(['user_type' => UserType::SuperAdmin]);
        $admin = User::factory()->create(['user_type' => UserType::Admin]);
        $student = User::factory()->create(['user_type' => UserType::Student]);

        // Super admin can create all types
        $this->assertTrue($superAdmin->can('createUserType', [User::class, UserType::Student]));
        $this->assertTrue($superAdmin->can('createUserType', [User::class, UserType::Driver]));
        $this->assertTrue($superAdmin->can('createUserType', [User::class, UserType::Admin]));
        $this->assertTrue($superAdmin->can('createUserType', [User::class, UserType::SuperAdmin]));

        // Admin can create most types but not super admin
        $this->assertTrue($admin->can('createUserType', [User::class, UserType::Student]));
        $this->assertTrue($admin->can('createUserType', [User::class, UserType::Driver]));
        $this->assertTrue($admin->can('createUserType', [User::class, UserType::Admin]));
        $this->assertFalse($admin->can('createUserType', [User::class, UserType::SuperAdmin]));

        // Student cannot create any users
        $this->assertFalse($student->can('createUserType', [User::class, UserType::Student]));
    }

    public function test_policy_prevents_self_type_change(): void
    {
        $admin = User::factory()->create(['user_type' => UserType::Admin]);

        // Cannot change own type
        $this->assertFalse($admin->can('updateUserType', [$admin, UserType::SuperAdmin]));
        $this->assertFalse($admin->can('updateUserType', [$admin, UserType::Student]));
    }

    public function test_policy_protects_super_admin_type(): void
    {
        $superAdmin1 = User::factory()->create(['user_type' => UserType::SuperAdmin]);
        $superAdmin2 = User::factory()->create(['user_type' => UserType::SuperAdmin]);

        // Super admin type cannot be changed
        $this->assertFalse($superAdmin1->can('updateUserType', [$superAdmin2, UserType::Admin]));
    }

    public function test_policy_prevents_self_deletion(): void
    {
        $admin = User::factory()->create(['user_type' => UserType::Admin]);

        $this->assertFalse($admin->can('delete', $admin));
    }

    public function test_policy_protects_super_admin_from_deletion(): void
    {
        $admin = User::factory()->create(['user_type' => UserType::Admin]);
        $superAdmin = User::factory()->create(['user_type' => UserType::SuperAdmin]);

        $this->assertFalse($admin->can('delete', $superAdmin));
    }

    public function test_only_super_admin_can_force_delete(): void
    {
        $superAdmin = User::factory()->create(['user_type' => UserType::SuperAdmin]);
        $admin = User::factory()->create(['user_type' => UserType::Admin]);
        $student = User::factory()->create(['user_type' => UserType::Student]);

        $this->assertTrue($superAdmin->can('forceDelete', $student));
        $this->assertFalse($admin->can('forceDelete', $student));
    }

    public function test_has_any_permission_works_correctly(): void
    {
        $admin = User::factory()->create(['user_type' => UserType::Admin]);
        $student = User::factory()->create(['user_type' => UserType::Student]);

        $this->assertTrue($admin->hasAnyPermission([
            Permission::ViewUsers,
            Permission::CreateSuperAdmin,
        ]));

        $this->assertFalse($student->hasAnyPermission([
            Permission::ViewUsers,
            Permission::CreateUsers,
        ]));
    }

    public function test_has_all_permissions_works_correctly(): void
    {
        $superAdmin = User::factory()->create(['user_type' => UserType::SuperAdmin]);
        $admin = User::factory()->create(['user_type' => UserType::Admin]);

        $this->assertTrue($superAdmin->hasAllPermissions([
            Permission::ViewUsers,
            Permission::CreateSuperAdmin,
        ]));

        $this->assertFalse($admin->hasAllPermissions([
            Permission::ViewUsers,
            Permission::CreateSuperAdmin,
        ]));
    }

    public function test_acl_integration_with_controller(): void
    {
        $admin = User::factory()->create(['user_type' => UserType::Admin]);
        $student = User::factory()->create(['user_type' => UserType::Student]);

        Sanctum::actingAs($admin);
        $response = $this->getJson('/api/v1/users');
        $response->assertStatus(200);

        Sanctum::actingAs($student);
        $response = $this->getJson('/api/v1/users');
        $response->assertStatus(403);
    }

    public function test_acl_integration_with_form_request(): void
    {
        $admin = User::factory()->create(['user_type' => UserType::Admin]);
        $student = User::factory()->create(['user_type' => UserType::Student]);

        // Admin can create users
        Sanctum::actingAs($admin);
        $response = $this->postJson('/api/v1/users', [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'cpf' => '12345678901',
            'rg' => '123456789',
            'phone_contact' => '11999999999',
            'password' => 'password123',
            'user_type' => 'student',
        ]);
        $response->assertStatus(201);

        // Student cannot create users
        Sanctum::actingAs($student);
        $response = $this->postJson('/api/v1/users', [
            'name' => 'Another User',
            'email' => 'another@example.com',
            'cpf' => '98765432109',
            'rg' => '987654321',
            'phone_contact' => '11888888888',
            'password' => 'password123',
            'user_type' => 'student',
        ]);
        $response->assertStatus(403);
    }
}
