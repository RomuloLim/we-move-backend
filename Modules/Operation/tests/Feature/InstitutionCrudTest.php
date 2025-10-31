<?php

namespace Modules\Operation\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Modules\Operation\Models\Institution;
use Modules\User\Enums\UserType;
use Modules\User\Models\User;
use Tests\TestCase;

class InstitutionCrudTest extends TestCase
{
    use RefreshDatabase;

    private function userActingAs(UserType $userType): void
    {
        $user = User::factory()->create(['user_type' => $userType->value]);

        Sanctum::actingAs($user);
    }

    public function test_admin_can_create_institution(): void
    {
        $this->userActingAs(UserType::Admin);

        $data = [
            'name' => 'Universidade Federal',
            'acronym' => 'UF',
            'city' => 'São Paulo',
            'state' => 'SP',
        ];

        $response = $this->postJson('/api/v1/institutions', $data);
        $response->assertCreated();
        $this->assertDatabaseHas('institutions', $data);
    }

    public function test_admin_can_list_institutions(): void
    {
        $this->userActingAs(UserType::Admin);

        Institution::factory()->create(['name' => 'Test University']);

        $response = $this->getJson('/api/v1/institutions');
        $response->assertOk()->assertJsonFragment(['name' => 'Test University']);
    }

    public function test_admin_can_show_institution(): void
    {
        $this->userActingAs(UserType::Admin);

        $institution = Institution::factory()->create(['name' => 'Show University']);

        $response = $this->getJson('/api/v1/institutions/' . $institution->id);
        $response->assertOk()->assertJsonFragment(['name' => 'Show University']);
    }

    public function test_admin_can_update_institution(): void
    {
        $this->userActingAs(UserType::Admin);

        $institution = Institution::factory()->create(['name' => 'Old Name']);
        $data = [
            'name' => 'New Name',
            'city' => 'Rio de Janeiro',
            'state' => 'RJ',
        ];

        $response = $this->putJson('/api/v1/institutions/' . $institution->id, $data);
        $response->assertOk();
        $this->assertDatabaseHas('institutions', ['name' => 'New Name']);
    }

    public function test_admin_can_delete_institution(): void
    {
        $this->userActingAs(UserType::Admin);

        $institution = Institution::factory()->create();

        $response = $this->deleteJson('/api/v1/institutions/' . $institution->id);
        $response->assertOk();
        $this->assertDatabaseMissing('institutions', ['id' => $institution->id]);
    }

    public function test_student_can_view_institutions(): void
    {
        $this->userActingAs(UserType::Student);

        Institution::factory()->create(['name' => 'Student View Institution']);

        $response = $this->getJson('/api/v1/institutions');
        $response->assertOk()->assertJsonFragment(['name' => 'Student View Institution']);
    }

    public function test_student_cannot_create_institution(): void
    {
        $this->userActingAs(UserType::Student);

        $data = [
            'name' => 'Unauthorized Institution',
            'city' => 'São Paulo',
            'state' => 'SP',
        ];

        $response = $this->postJson('/api/v1/institutions', $data);
        $response->assertForbidden();
    }
}
