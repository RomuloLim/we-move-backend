<?php

namespace Modules\Logistics\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Modules\Logistics\Enums\TripStatus;
use Modules\Logistics\Models\{Route, Stop, Trip, Vehicle};
use Modules\User\Enums\UserType;
use Modules\User\Models\User;
use Tests\TestCase;

class VehicleAvailabilityFilterTest extends TestCase
{
    use RefreshDatabase;

    private function createUserWithPermission(): User
    {
        return User::factory()->create([
            'user_type' => UserType::Admin->value,
        ]);
    }

    private function createDriver(): User
    {
        return User::factory()->create([
            'user_type' => UserType::Driver->value,
        ]);
    }

    private function createRoute(): Route
    {
        $route = Route::factory()->create();
        Stop::factory()->count(2)->create(['route_id' => $route->id]);

        return $route;
    }

    public function test_can_list_all_vehicles(): void
    {
        $user = $this->createUserWithPermission();
        Sanctum::actingAs($user);

        // Criar veículos disponíveis
        Vehicle::factory()->count(3)->create();

        // Criar veículo em uso
        $route = $this->createRoute();
        $driver = $this->createDriver();
        $vehicle = Vehicle::factory()->create();
        Trip::factory()->create([
            'route_id' => $route->id,
            'driver_id' => $driver->id,
            'vehicle_id' => $vehicle->id,
            'status' => TripStatus::InProgress,
        ]);

        $response = $this->getJson('/api/v1/vehicles?availability=all');

        $response->assertOk();
        $response->assertJsonCount(4, 'data');
    }

    public function test_can_filter_available_vehicles(): void
    {
        $user = $this->createUserWithPermission();
        Sanctum::actingAs($user);

        // Criar veículos disponíveis
        Vehicle::factory()->count(3)->create();

        // Criar veículos em uso
        $route = $this->createRoute();
        $driver = $this->createDriver();
        $vehicleInUse1 = Vehicle::factory()->create();
        $vehicleInUse2 = Vehicle::factory()->create();

        Trip::factory()->create([
            'route_id' => $route->id,
            'driver_id' => $driver->id,
            'vehicle_id' => $vehicleInUse1->id,
            'status' => TripStatus::InProgress,
        ]);

        Trip::factory()->create([
            'route_id' => $route->id,
            'driver_id' => $driver->id,
            'vehicle_id' => $vehicleInUse2->id,
            'status' => TripStatus::InProgress,
        ]);

        $response = $this->getJson('/api/v1/vehicles?availability=available');

        $response->assertOk();
        $response->assertJsonCount(3, 'data');
    }

    public function test_can_filter_vehicles_in_use(): void
    {
        $user = $this->createUserWithPermission();
        Sanctum::actingAs($user);

        // Criar veículos disponíveis
        Vehicle::factory()->count(3)->create();

        // Criar veículos em uso
        $route = $this->createRoute();
        $driver = $this->createDriver();
        $vehicleInUse1 = Vehicle::factory()->create();
        $vehicleInUse2 = Vehicle::factory()->create();

        Trip::factory()->create([
            'route_id' => $route->id,
            'driver_id' => $driver->id,
            'vehicle_id' => $vehicleInUse1->id,
            'status' => TripStatus::InProgress,
        ]);

        Trip::factory()->create([
            'route_id' => $route->id,
            'driver_id' => $driver->id,
            'vehicle_id' => $vehicleInUse2->id,
            'status' => TripStatus::InProgress,
        ]);

        $response = $this->getJson('/api/v1/vehicles?availability=in_use');

        $response->assertOk();
        $response->assertJsonCount(2, 'data');
    }

    public function test_completed_trips_do_not_affect_availability(): void
    {
        $user = $this->createUserWithPermission();
        Sanctum::actingAs($user);

        $route = $this->createRoute();
        $driver = $this->createDriver();
        $vehicle = Vehicle::factory()->create();

        // Criar viagem completada
        Trip::factory()->create([
            'route_id' => $route->id,
            'driver_id' => $driver->id,
            'vehicle_id' => $vehicle->id,
            'status' => TripStatus::Completed,
        ]);

        $response = $this->getJson('/api/v1/vehicles?availability=available');

        $response->assertOk();
        $response->assertJsonCount(1, 'data');
    }

    public function test_scheduled_trips_do_not_affect_availability(): void
    {
        $user = $this->createUserWithPermission();
        Sanctum::actingAs($user);

        $route = $this->createRoute();
        $driver = $this->createDriver();
        $vehicle = Vehicle::factory()->create();

        // Criar viagem agendada
        Trip::factory()->create([
            'route_id' => $route->id,
            'driver_id' => $driver->id,
            'vehicle_id' => $vehicle->id,
            'status' => TripStatus::Scheduled,
        ]);

        $response = $this->getJson('/api/v1/vehicles?availability=available');

        $response->assertOk();
        $response->assertJsonCount(1, 'data');
    }

    public function test_can_list_vehicles_without_availability_filter(): void
    {
        $user = $this->createUserWithPermission();
        Sanctum::actingAs($user);

        // Criar veículos disponíveis
        Vehicle::factory()->count(3)->create();

        // Criar veículo em uso
        $route = $this->createRoute();
        $driver = $this->createDriver();
        $vehicle = Vehicle::factory()->create();
        Trip::factory()->create([
            'route_id' => $route->id,
            'driver_id' => $driver->id,
            'vehicle_id' => $vehicle->id,
            'status' => TripStatus::InProgress,
        ]);

        // Sem passar o parâmetro availability
        $response = $this->getJson('/api/v1/vehicles');

        $response->assertOk();
        $response->assertJsonCount(4, 'data');
    }

    public function test_invalid_availability_filter_returns_validation_error(): void
    {
        $user = $this->createUserWithPermission();
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/v1/vehicles?availability=invalid');

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('availability');
    }
}
