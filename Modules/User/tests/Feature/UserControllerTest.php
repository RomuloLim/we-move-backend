<?php

namespace Modules\User\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Modules\User\Enums\UserType;
use Modules\User\Models\User;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_super_admin_can_list_users(): void
    {
        // Arrange
        $superAdmin = User::factory()->create(['user_type' => UserType::SuperAdmin]);
        User::factory(3)->create(['user_type' => UserType::Student]);

        Sanctum::actingAs($superAdmin);

        // Act
        $response = $this->getJson('/api/v1/users');

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'email',
                        'user_type',
                        'user_type_label',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ]);
    }

    public function test_admin_can_list_users(): void
    {
        // Arrange
        $admin = User::factory()->create(['user_type' => UserType::Admin]);

        Sanctum::actingAs($admin);

        // Act
        $response = $this->getJson('/api/v1/users');

        // Assert
        $response->assertStatus(200);
    }

    public function test_student_cannot_list_users(): void
    {
        // Arrange
        $student = User::factory()->create(['user_type' => UserType::Student]);

        Sanctum::actingAs($student);

        // Act
        $response = $this->getJson('/api/v1/users');

        // Assert
        $response->assertStatus(403);
    }

    public function test_super_admin_can_create_any_user_type(): void
    {
        // Arrange
        $superAdmin = User::factory()->create(['user_type' => UserType::SuperAdmin]);

        Sanctum::actingAs($superAdmin);

        // Act
        $response = $this->postJson('/api/v1/users', [
            'name' => 'New Admin',
            'email' => 'newadmin@example.com',
            'cpf' => '12345678901',
            'rg' => '123456789',
            'phone_contact' => '11999999999',
            'password' => 'password123',
            'user_type' => 'admin',
        ]);

        // Assert
        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id',
                    'name',
                    'email',
                    'user_type',
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'newadmin@example.com',
            'user_type' => 'admin',
        ]);
    }

    public function test_admin_can_create_student_and_driver(): void
    {
        // Arrange
        $admin = User::factory()->create(['user_type' => UserType::Admin]);

        Sanctum::actingAs($admin);

        // Act & Assert - Create Student
        $response = $this->postJson('/api/v1/users', [
            'name' => 'New Student',
            'email' => 'student@example.com',
            'cpf' => '12345678901',
            'rg' => '123456789',
            'phone_contact' => '11999999999',
            'password' => 'password123',
            'user_type' => 'student',
        ]);

        $response->assertStatus(201);

        // Act & Assert - Create Driver
        $response = $this->postJson('/api/v1/users', [
            'name' => 'New Driver',
            'email' => 'driver@example.com',
            'cpf' => '98765432101',
            'rg' => '987654321',
            'phone_contact' => '11888888888',
            'password' => 'password123',
            'user_type' => 'driver',
        ]);

        $response->assertStatus(201);
    }

    public function test_admin_cannot_create_super_admin(): void
    {
        // Arrange
        $admin = User::factory()->create(['user_type' => UserType::Admin]);

        Sanctum::actingAs($admin);

        // Act
        $response = $this->postJson('/api/v1/users', [
            'name' => 'New Super Admin',
            'email' => 'superadmin@example.com',
            'cpf' => '11111111111',
            'rg' => '111111111',
            'phone_contact' => '11777777777',
            'password' => 'password123',
            'user_type' => 'super-admin',
        ]);

        // Assert
        $response->assertStatus(403);
    }

    public function test_super_admin_can_update_user_type(): void
    {
        // Arrange
        $superAdmin = User::factory()->create(['user_type' => UserType::SuperAdmin]);
        $student = User::factory()->create(['user_type' => UserType::Student]);

        Sanctum::actingAs($superAdmin);

        // Act
        $response = $this->putJson("/api/v1/users/{$student->id}/type", [
            'user_type' => 'driver',
        ]);

        // Assert
        $response->assertStatus(200);

        $student->refresh();
        $this->assertEquals(UserType::Driver, $student->user_type);
    }

    public function test_admin_cannot_update_user_type_for_super_admin(): void
    {
        // Arrange
        $admin = User::factory()->create(['user_type' => UserType::Admin]);
        $student = User::factory()->create(['user_type' => UserType::Student]);

        Sanctum::actingAs($admin);

        // Act
        $response = $this->putJson("/api/v1/users/{$student->id}/type", [
            'user_type' => 'super-admin',
        ]);

        // Assert
        $response->assertUnprocessable()
            ->assertJsonStructure([
                'message',
                'errors',
            ]);
    }

    public function test_unauthenticated_user_cannot_access_user_endpoints(): void
    {
        $response = $this->getJson('/api/v1/users');
        $response->assertStatus(401);

        $response = $this->postJson('/api/v1/users', []);
        $response->assertStatus(401);
    }

    public function test_guest_can_register_as_student(): void
    {
        // Act
        $response = $this->postJson('/api/v1/register', [
            'name' => 'New Student',
            'email' => 'student@example.com',
            'cpf' => '12345678901',
            'rg' => '123456789',
            'phone_contact' => '11999999999',
            'password' => 'password123',
        ]);

        // Assert
        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id',
                    'name',
                    'email',
                    'user_type',
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'student@example.com',
            'user_type' => UserType::Student->value,
        ]);
    }

    public function test_driver_cannot_create_users(): void
    {
        // Arrange
        $driver = User::factory()->create(['user_type' => UserType::Driver]);

        Sanctum::actingAs($driver);

        // Act
        $response = $this->postJson('/api/v1/users', [
            'name' => 'New Student',
            'email' => 'student@example.com',
            'cpf' => '12345678901',
            'rg' => '123456789',
            'phone_contact' => '11999999999',
            'password' => 'password123',
            'user_type' => 'student',
        ]);

        // Assert
        $response->assertStatus(403);
    }

    public function test_guest_cannot_access_admin_create_user_route(): void
    {
        // Act
        $response = $this->postJson('/api/v1/users', [
            'name' => 'New Admin',
            'email' => 'admin@example.com',
            'cpf' => '12345678901',
            'rg' => '123456789',
            'phone_contact' => '11999999999',
            'password' => 'password123',
            'user_type' => 'admin',
        ]);

        // Assert
        $response->assertStatus(401);
    }

    public function test_student_cannot_create_users(): void
    {
        // Arrange
        $student = User::factory()->create(['user_type' => UserType::Student]);

        Sanctum::actingAs($student);

        // Act
        $response = $this->postJson('/api/v1/users', [
            'name' => 'Another Student',
            'email' => 'student2@example.com',
            'cpf' => '12345678901',
            'rg' => '123456789',
            'phone_contact' => '11999999999',
            'password' => 'password123',
            'user_type' => 'student',
        ]);

        // Assert
        $response->assertStatus(403);
    }
}
