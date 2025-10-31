<?php

namespace Modules\Operation\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Modules\Operation\Models\Vehicle;
use Modules\User\Enums\UserType;
use Modules\User\Models\User;
use Tests\TestCase;

class VehicleCrudTest extends TestCase
{
    use RefreshDatabase;

    private function userActingAs(UserType $userType): void
    {
        $user = User::factory()->create(['user_type' => $userType->value]);

        Sanctum::actingAs($user);
    }

    public function test_can_create_vehicle(): void
    {
        $this->userActingAs(UserType::Admin);

        $data = [
            'license_plate' => 'ABC1234',
            'model' => 'Caminhão',
            'capacity' => 10,
        ];
        $response = $this->postJson('/api/v1/vehicles', $data);
        $response->assertCreated();
        $this->assertDatabaseHas('vehicles', $data);
    }

    public function test_can_list_vehicles(): void
    {
        $this->userActingAs(UserType::Admin);

        Vehicle::factory()->create(['license_plate' => 'XYZ9876']);
        $response = $this->getJson('/api/v1/vehicles');
        $response->assertOk()->assertJsonFragment(['license_plate' => 'XYZ9876']);
    }

    public function test_can_show_vehicle(): void
    {
        $this->userActingAs(UserType::Admin);

        $vehicle = Vehicle::factory()->create(['license_plate' => 'SHOW123']);
        $response = $this->getJson('/api/v1/vehicles/' . $vehicle->id);
        $response->assertOk()->assertJsonFragment(['license_plate' => 'SHOW123']);
    }

    public function test_can_update_vehicle(): void
    {
        $this->userActingAs(UserType::Admin);

        $vehicle = Vehicle::factory()->create(['license_plate' => 'UPD123']);
        $data = ['license_plate' => 'UPD999', 'model' => 'Van', 'capacity' => 5];
        $response = $this->putJson('/api/v1/vehicles/' . $vehicle->id, $data);
        $response->assertOk();
        $this->assertDatabaseHas('vehicles', $data);
    }

    public function test_can_delete_vehicle(): void
    {
        $this->userActingAs(UserType::Admin);

        $vehicle = Vehicle::factory()->create(['license_plate' => 'DEL123']);
        $response = $this->deleteJson('/api/v1/vehicles/' . $vehicle->id);
        $response->assertOk();
        $this->assertDatabaseMissing('vehicles', ['id' => $vehicle->id]);
    }
}
