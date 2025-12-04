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
            ->assertJsonStructure([
                'message',
                'data' => [
                    'trip',
                    'summary' => [
                        'route_name',
                        'total_boardings',
                        'duration',
                    ],
                ],
            ])
            ->assertJsonPath('data.trip.status', TripStatus::Completed->value);

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

        // Create first trip (in progress)
        $this->postJson('/api/v1/trips/start', [
            'route_id' => $route->id,
            'vehicle_id' => $vehicle->id,
            'trip_date' => $tripDate,
        ])->assertCreated();

        // Try to create another trip for same route and date with different driver while first is still in progress
        $anotherDriver = User::factory()->create(['user_type' => UserType::Driver->value]);
        $anotherVehicle = Vehicle::factory()->create();
        Sanctum::actingAs($anotherDriver);

        $response = $this->postJson('/api/v1/trips/start', [
            'route_id' => $route->id,
            'vehicle_id' => $anotherVehicle->id,
            'trip_date' => $tripDate,
        ]);

        $response->assertStatus(400)
            ->assertJson([
                'message' => 'Já existe uma viagem em progresso para esta rota na data selecionada.',
            ]);
    }

    public function test_can_start_trip_after_previous_trip_completed(): void
    {
        $driver = $this->actingAsDriver();
        $route = Route::factory()->create();
        $vehicle = Vehicle::factory()->create();
        $tripDate = now()->format('Y-m-d');

        // Create and complete first trip
        $firstTripResponse = $this->postJson('/api/v1/trips/start', [
            'route_id' => $route->id,
            'vehicle_id' => $vehicle->id,
            'trip_date' => $tripDate,
        ])->assertCreated();

        $firstTripId = $firstTripResponse->json('data.id');
        $this->patchJson("/api/v1/trips/{$firstTripId}/complete")->assertOk();

        // Now another driver should be able to start a new trip for the same route and date
        $anotherDriver = User::factory()->create(['user_type' => UserType::Driver->value]);
        $anotherVehicle = Vehicle::factory()->create();
        Sanctum::actingAs($anotherDriver);

        $response = $this->postJson('/api/v1/trips/start', [
            'route_id' => $route->id,
            'vehicle_id' => $anotherVehicle->id,
            'trip_date' => $tripDate,
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('trips', [
            'route_id' => $route->id,
            'driver_id' => $anotherDriver->id,
            'trip_date' => $tripDate,
            'status' => TripStatus::InProgress->value,
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

    public function test_driver_can_get_their_active_trip(): void
    {
        $driver = $this->actingAsDriver();
        $route = Route::factory()->create();
        $vehicle = Vehicle::factory()->create();

        // Start a trip
        $this->postJson('/api/v1/trips/start', [
            'route_id' => $route->id,
            'vehicle_id' => $vehicle->id,
            'trip_date' => now()->format('Y-m-d'),
        ])->assertCreated();

        // Get active trip
        $response = $this->getJson('/api/v1/trips/my-active-trip');

        $response->assertOk()
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
            ])
            ->assertJson([
                'data' => [
                    'driver_id' => $driver->id,
                    'route_id' => $route->id,
                    'vehicle_id' => $vehicle->id,
                    'status' => TripStatus::InProgress->value,
                ],
            ]);
    }

    public function test_driver_gets_404_when_no_active_trip_exists(): void
    {
        $this->actingAsDriver();

        $response = $this->getJson('/api/v1/trips/my-active-trip');

        $response->assertNotFound()
            ->assertJson([
                'message' => 'Nenhuma viagem ativa encontrada.',
            ]);
    }

    public function test_driver_only_sees_their_own_active_trip(): void
    {
        // Driver 1 starts a trip
        $driver1 = User::factory()->create(['user_type' => UserType::Driver->value]);
        $route1 = Route::factory()->create();
        $vehicle1 = Vehicle::factory()->create();

        Sanctum::actingAs($driver1);
        $this->postJson('/api/v1/trips/start', [
            'route_id' => $route1->id,
            'vehicle_id' => $vehicle1->id,
            'trip_date' => now()->format('Y-m-d'),
        ])->assertCreated();

        // Driver 2 starts another trip
        $driver2 = User::factory()->create(['user_type' => UserType::Driver->value]);
        $route2 = Route::factory()->create();
        $vehicle2 = Vehicle::factory()->create();

        Sanctum::actingAs($driver2);
        $this->postJson('/api/v1/trips/start', [
            'route_id' => $route2->id,
            'vehicle_id' => $vehicle2->id,
            'trip_date' => now()->format('Y-m-d'),
        ])->assertCreated();

        // Driver 2 should only see their own active trip
        $response = $this->getJson('/api/v1/trips/my-active-trip');

        $response->assertOk()
            ->assertJson([
                'data' => [
                    'driver_id' => $driver2->id,
                    'route_id' => $route2->id,
                ],
            ]);

        // Verify driver 2 doesn't see driver 1's trip
        $response->assertJsonMissing([
            'driver_id' => $driver1->id,
        ]);
    }

    public function test_completed_trip_is_not_returned_as_active(): void
    {
        $driver = $this->actingAsDriver();
        $route = Route::factory()->create();
        $vehicle = Vehicle::factory()->create();

        // Start and complete a trip
        $startResponse = $this->postJson('/api/v1/trips/start', [
            'route_id' => $route->id,
            'vehicle_id' => $vehicle->id,
            'trip_date' => now()->format('Y-m-d'),
        ])->assertCreated();

        $tripId = $startResponse->json('data.id');
        $this->patchJson("/api/v1/trips/{$tripId}/complete")->assertOk();

        // Try to get active trip
        $response = $this->getJson('/api/v1/trips/my-active-trip');

        $response->assertNotFound()
            ->assertJson([
                'message' => 'Nenhuma viagem ativa encontrada.',
            ]);
    }

    public function test_complete_trip_returns_summary_with_route_name_boardings_and_duration(): void
    {
        $driver = $this->actingAsDriver();
        $route = Route::factory()->create(['route_name' => 'Rota Centro-Universidade']);
        $vehicle = Vehicle::factory()->create();

        // Start a trip
        $startResponse = $this->postJson('/api/v1/trips/start', [
            'route_id' => $route->id,
            'vehicle_id' => $vehicle->id,
            'trip_date' => now()->format('Y-m-d'),
        ])->assertCreated();

        $tripId = $startResponse->json('data.id');
        $trip = Trip::find($tripId);

        // Create some boardings
        $student1 = \Modules\Operation\Models\Student::factory()->create();
        $student2 = \Modules\Operation\Models\Student::factory()->create();
        $stop = \Modules\Logistics\Models\Stop::factory()->create(['route_id' => $route->id]);

        \Modules\Logistics\Models\Boarding::factory()->create([
            'trip_id' => $tripId,
            'student_id' => $student1->id,
            'stop_id' => $stop->id,
        ]);
        \Modules\Logistics\Models\Boarding::factory()->create([
            'trip_id' => $tripId,
            'student_id' => $student2->id,
            'stop_id' => $stop->id,
        ]);

        // Wait a bit to have some duration
        sleep(1);

        // Complete the trip
        $response = $this->patchJson("/api/v1/trips/{$tripId}/complete");

        $response->assertOk()
            ->assertJsonStructure([
                'message',
                'data' => [
                    'trip',
                    'summary' => [
                        'route_name',
                        'total_boardings',
                        'duration',
                    ],
                ],
            ])
            ->assertJsonPath('data.summary.route_name', 'Rota Centro-Universidade')
            ->assertJsonPath('data.summary.total_boardings', 2);

        $this->assertGreaterThanOrEqual('0s', $response->json('data.summary.duration'));
        $this->assertNotEmpty($response->json('data.summary.duration'));
    }

    public function test_student_can_get_active_trip_when_boarded(): void
    {
        $student = $this->actingAsStudent();
        $driver = User::factory()->create(['user_type' => UserType::Driver->value]);
        $route = Route::factory()->create();
        $vehicle = Vehicle::factory()->create();

        // Create an active trip
        $trip = Trip::factory()->create([
            'route_id' => $route->id,
            'driver_id' => $driver->id,
            'vehicle_id' => $vehicle->id,
            'status' => TripStatus::InProgress,
        ]);

        $stop = \Modules\Logistics\Models\Stop::factory()->create(['route_id' => $route->id]);
        $studentModel = \Modules\Operation\Models\Student::factory()->create(['user_id' => $student->id]);

        // Board the student (without landing)
        \Modules\Logistics\Models\Boarding::factory()->create([
            'trip_id' => $trip->id,
            'student_id' => $studentModel->id,
            'stop_id' => $stop->id,
            'landed_at' => null,
        ]);

        $response = $this->getJson('/api/v1/trips/my-active-trip-as-student');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'route',
                    'driver',
                    'vehicle',
                    'status',
                ],
            ])
            ->assertJsonPath('data.id', $trip->id)
            ->assertJsonPath('data.status', TripStatus::InProgress->value);
    }

    public function test_student_cannot_get_active_trip_when_not_boarded(): void
    {
        $student = $this->actingAsStudent();
        $driver = User::factory()->create(['user_type' => UserType::Driver->value]);
        $route = Route::factory()->create();
        $vehicle = Vehicle::factory()->create();

        // Create an active trip
        Trip::factory()->create([
            'route_id' => $route->id,
            'driver_id' => $driver->id,
            'vehicle_id' => $vehicle->id,
            'status' => TripStatus::InProgress,
        ]);

        // Student is not boarded
        $response = $this->getJson('/api/v1/trips/my-active-trip-as-student');

        $response->assertNotFound()
            ->assertJson([
                'message' => 'Nenhuma viagem ativa encontrada.',
            ]);
    }

    public function test_student_cannot_get_active_trip_when_already_landed(): void
    {
        $student = $this->actingAsStudent();
        $driver = User::factory()->create(['user_type' => UserType::Driver->value]);
        $route = Route::factory()->create();
        $vehicle = Vehicle::factory()->create();

        // Create an active trip
        $trip = Trip::factory()->create([
            'route_id' => $route->id,
            'driver_id' => $driver->id,
            'vehicle_id' => $vehicle->id,
            'status' => TripStatus::InProgress,
        ]);

        $stop = \Modules\Logistics\Models\Stop::factory()->create(['route_id' => $route->id]);
        $studentModel = \Modules\Operation\Models\Student::factory()->create(['user_id' => $student->id]);

        // Board and land the student
        \Modules\Logistics\Models\Boarding::factory()->create([
            'trip_id' => $trip->id,
            'student_id' => $studentModel->id,
            'stop_id' => $stop->id,
            'landed_at' => now(),
        ]);

        $response = $this->getJson('/api/v1/trips/my-active-trip-as-student');

        $response->assertNotFound()
            ->assertJson([
                'message' => 'Nenhuma viagem ativa encontrada.',
            ]);
    }

    public function test_student_cannot_get_completed_trip_even_if_boarded(): void
    {
        $student = $this->actingAsStudent();
        $driver = User::factory()->create(['user_type' => UserType::Driver->value]);
        $route = Route::factory()->create();
        $vehicle = Vehicle::factory()->create();

        // Create a completed trip
        $trip = Trip::factory()->create([
            'route_id' => $route->id,
            'driver_id' => $driver->id,
            'vehicle_id' => $vehicle->id,
            'status' => TripStatus::Completed,
        ]);

        $stop = \Modules\Logistics\Models\Stop::factory()->create(['route_id' => $route->id]);
        $studentModel = \Modules\Operation\Models\Student::factory()->create(['user_id' => $student->id]);

        // Board the student without landing (simulating they were boarded before completion)
        \Modules\Logistics\Models\Boarding::factory()->create([
            'trip_id' => $trip->id,
            'student_id' => $studentModel->id,
            'stop_id' => $stop->id,
            'landed_at' => null,
        ]);

        $response = $this->getJson('/api/v1/trips/my-active-trip-as-student');

        $response->assertNotFound()
            ->assertJson([
                'message' => 'Nenhuma viagem ativa encontrada.',
            ]);
    }
}
