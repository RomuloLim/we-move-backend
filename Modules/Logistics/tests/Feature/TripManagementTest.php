<?php

namespace Modules\Logistics\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Modules\Logistics\Enums\TripStatus;
use Modules\Logistics\Models\{Route, Trip, Vehicle};
use Modules\User\Enums\UserType;
use Modules\User\Models\User;
use Tests\TestCase;

class TripManagementTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsDriver(): User
    {
        $driver = User::factory()->create(['user_type' => UserType::Driver->value]);
        Sanctum::actingAs($driver);

        return $driver;
    }

    private function actingAsAdmin(): User
    {
        $admin = User::factory()->create(['user_type' => UserType::Admin->value]);
        Sanctum::actingAs($admin);

        return $admin;
    }

    private function actingAsStudent(): User
    {
        $student = User::factory()->create(['user_type' => UserType::Student->value]);
        Sanctum::actingAs($student);

        return $student;
    }

    public function test_driver_can_start_a_trip(): void
    {
        $driver = $this->actingAsDriver();
        $route = Route::factory()->create();
        $vehicle = Vehicle::factory()->create();

        $response = $this->postJson('/api/v1/trips/start', [
            'route_id' => $route->id,
            'vehicle_id' => $vehicle->id,
            'trip_date' => now()->format('Y-m-d'),
        ]);

        $response->assertCreated()
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'route_id',
                    'driver_id',
                    'vehicle_id',
                    'trip_date',
                    'status',
                    'status_label',
                ],
            ]);

        $this->assertDatabaseHas('trips', [
            'route_id' => $route->id,
            'driver_id' => $driver->id,
            'vehicle_id' => $vehicle->id,
            'status' => TripStatus::InProgress->value,
        ]);
    }

    public function test_driver_cannot_start_multiple_trips_simultaneously(): void
    {
        $driver = $this->actingAsDriver();
        $route1 = Route::factory()->create();
        $route2 = Route::factory()->create();
        $vehicle = Vehicle::factory()->create();

        // Start first trip
        $this->postJson('/api/v1/trips/start', [
            'route_id' => $route1->id,
            'vehicle_id' => $vehicle->id,
            'trip_date' => now()->format('Y-m-d'),
        ])->assertCreated();

        // Try to start second trip
        $response = $this->postJson('/api/v1/trips/start', [
            'route_id' => $route2->id,
            'vehicle_id' => $vehicle->id,
            'trip_date' => now()->format('Y-m-d'),
        ]);

        $response->assertStatus(400)
            ->assertJson([
                'message' => 'Você já possui uma viagem em progresso. Finalize-a antes de iniciar outra.',
            ]);
    }

    public function test_driver_can_complete_their_trip(): void
    {
        $driver = $this->actingAsDriver();
        $trip = Trip::factory()->inProgress()->create(['driver_id' => $driver->id]);

        $response = $this->patchJson("/api/v1/trips/{$trip->id}/complete");

        $response->assertOk()
            ->assertJsonPath('data.status', TripStatus::Completed->value);

        $this->assertDatabaseHas('trips', [
            'id' => $trip->id,
            'status' => TripStatus::Completed->value,
        ]);
    }

    public function test_driver_cannot_complete_another_drivers_trip(): void
    {
        $this->actingAsDriver();
        $anotherDriver = User::factory()->create(['user_type' => UserType::Driver->value]);
        $trip = Trip::factory()->inProgress()->create(['driver_id' => $anotherDriver->id]);

        $response = $this->patchJson("/api/v1/trips/{$trip->id}/complete");

        $response->assertStatus(400)
            ->assertJson([
                'message' => 'Você não tem permissão para finalizar esta viagem.',
            ]);
    }

    public function test_driver_cannot_complete_already_completed_trip(): void
    {
        $driver = $this->actingAsDriver();
        $trip = Trip::factory()->completed()->create(['driver_id' => $driver->id]);

        $response = $this->patchJson("/api/v1/trips/{$trip->id}/complete");

        $response->assertStatus(400)
            ->assertJson([
                'message' => 'Apenas viagens em progresso podem ser finalizadas.',
            ]);
    }

    public function test_any_authenticated_user_can_view_active_trips(): void
    {
        $this->actingAsDriver();

        $inProgressTrip = Trip::factory()->inProgress()->create();
        $completedTrip = Trip::factory()->completed()->create();
        $scheduledTrip = Trip::factory()->scheduled()->create();

        $response = $this->getJson('/api/v1/trips/active');

        $response->assertOk()
            ->assertJsonCount(1, 'data');

        $this->assertEquals($inProgressTrip->id, $response->json('data.0.id'));
    }

    public function test_active_trips_can_be_filtered_by_user_routes(): void
    {
        $driver = $this->actingAsDriver();
        $route1 = Route::factory()->create();
        $route2 = Route::factory()->create();

        // Link route1 to driver
        $driver->routes()->attach($route1->id);

        // Create trips for both routes
        $trip1 = Trip::factory()->inProgress()->create(['route_id' => $route1->id]);
        $trip2 = Trip::factory()->inProgress()->create(['route_id' => $route2->id]);

        $response = $this->getJson("/api/v1/trips/active?user_id={$driver->id}");

        $response->assertOk()
            ->assertJsonCount(1, 'data');

        $this->assertEquals($trip1->id, $response->json('data.0.id'));
    }

    public function test_student_cannot_start_trip(): void
    {
        $this->actingAsStudent();
        $route = Route::factory()->create();
        $vehicle = Vehicle::factory()->create();

        $response = $this->postJson('/api/v1/trips/start', [
            'route_id' => $route->id,
            'vehicle_id' => $vehicle->id,
            'trip_date' => now()->format('Y-m-d'),
        ]);

        $response->assertForbidden();
    }

    public function test_student_cannot_complete_trip(): void
    {
        $this->actingAsStudent();
        $trip = Trip::factory()->inProgress()->create();

        $response = $this->patchJson("/api/v1/trips/{$trip->id}/complete");

        $response->assertForbidden();
    }

    public function test_start_trip_requires_valid_data(): void
    {
        $this->actingAsDriver();

        $response = $this->postJson('/api/v1/trips/start', [
            'route_id' => 999999,
            'vehicle_id' => 999999,
            'trip_date' => 'invalid-date',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['route_id', 'vehicle_id', 'trip_date']);
    }

    public function test_cannot_start_duplicate_trip_for_same_route_and_date(): void
    {
        $driver = $this->actingAsDriver();
        $route = Route::factory()->create();
        $vehicle = Vehicle::factory()->create();
        $tripDate = now()->format('Y-m-d');

        // Create first trip
        $this->postJson('/api/v1/trips/start', [
            'route_id' => $route->id,
            'vehicle_id' => $vehicle->id,
            'trip_date' => $tripDate,
        ])->assertCreated();

        // Complete it
        $trip = Trip::where('driver_id', $driver->id)->first();
        $trip->update(['status' => TripStatus::Completed]);

        // Try to create another trip for same route and date with different driver
        $anotherDriver = User::factory()->create(['user_type' => UserType::Driver->value]);
        Sanctum::actingAs($anotherDriver);

        $response = $this->postJson('/api/v1/trips/start', [
            'route_id' => $route->id,
            'vehicle_id' => $vehicle->id,
            'trip_date' => $tripDate,
        ]);

        $response->assertStatus(400)
            ->assertJson([
                'message' => 'Já existe uma viagem para esta rota na data selecionada.',
            ]);
    }

    public function test_admin_can_view_all_active_trips(): void
    {
        $this->actingAsAdmin();

        Trip::factory()->inProgress()->count(3)->create();
        Trip::factory()->completed()->count(2)->create();

        $response = $this->getJson('/api/v1/trips/active');

        $response->assertOk()
            ->assertJsonCount(3, 'data');
    }

    public function test_active_trips_include_relationships(): void
    {
        $this->actingAsDriver();
        $trip = Trip::factory()->inProgress()->create();

        $response = $this->getJson('/api/v1/trips/active');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'route' => [
                            'id',
                            'route_name',
                        ],
                        'driver' => [
                            'id',
                            'name',
                        ],
                        'vehicle' => [
                            'id',
                            'license_plate',
                        ],
                    ],
                ],
            ]);
    }

    public function test_driver_cannot_start_trip_with_vehicle_in_use(): void
    {
        $driver1 = $this->actingAsDriver();
        $driver2 = User::factory()->create(['user_type' => UserType::Driver->value]);
        $route1 = Route::factory()->create();
        $route2 = Route::factory()->create();
        $vehicle = Vehicle::factory()->create();

        // Driver 1 starts a trip with the vehicle
        $this->postJson('/api/v1/trips/start', [
            'route_id' => $route1->id,
            'vehicle_id' => $vehicle->id,
            'trip_date' => now()->format('Y-m-d'),
        ])->assertCreated();

        // Driver 2 tries to start a trip with the same vehicle
        Sanctum::actingAs($driver2);

        $response = $this->postJson('/api/v1/trips/start', [
            'route_id' => $route2->id,
            'vehicle_id' => $vehicle->id,
            'trip_date' => now()->format('Y-m-d'),
        ]);

        $response->assertStatus(400)
            ->assertJson([
                'message' => 'Este veículo já está sendo utilizado em outra viagem em andamento.',
            ]);
    }

    public function test_vehicle_can_be_reused_after_trip_completion(): void
    {
        $driver1 = $this->actingAsDriver();
        $route1 = Route::factory()->create();
        $vehicle = Vehicle::factory()->create();

        // Start first trip
        $response = $this->postJson('/api/v1/trips/start', [
            'route_id' => $route1->id,
            'vehicle_id' => $vehicle->id,
            'trip_date' => now()->format('Y-m-d'),
        ]);

        $tripId = $response->json('data.id');

        // Complete the trip
        $this->patchJson("/api/v1/trips/{$tripId}/complete")->assertOk();

        // Start another trip with the same vehicle
        $route2 = Route::factory()->create();
        $response = $this->postJson('/api/v1/trips/start', [
            'route_id' => $route2->id,
            'vehicle_id' => $vehicle->id,
            'trip_date' => now()->addDay()->format('Y-m-d'),
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('trips', [
            'vehicle_id' => $vehicle->id,
            'route_id' => $route2->id,
            'status' => TripStatus::InProgress->value,
        ]);
    }
}
