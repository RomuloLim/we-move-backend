<?php

namespace Modules\Logistics\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;
use Modules\Logistics\Enums\TripStatus;
use Modules\Logistics\Models\{Boarding, Route, Stop, Trip, Vehicle};
use Modules\Operation\Models\Student;
use Modules\User\Enums\UserType;
use Modules\User\Models\User;
use Tests\TestCase;

class BoardingManagementTest extends TestCase
{
    use RefreshDatabase;

    private function createDriver(): User
    {
        return User::factory()->create(['user_type' => UserType::Driver->value]);
    }

    private function createStudent(): Student
    {
        $user = User::factory()->create(['user_type' => UserType::Student->value]);

        return Student::factory()->create([
            'user_id' => $user->id,
            'qrcode_token' => Str::uuid()->toString(),
        ]);
    }

    private function createActiveTrip(User $driver): Trip
    {
        $route = Route::factory()->create();
        $vehicle = Vehicle::factory()->create();

        return Trip::factory()->create([
            'route_id' => $route->id,
            'driver_id' => $driver->id,
            'vehicle_id' => $vehicle->id,
            'status' => TripStatus::InProgress,
            'trip_date' => now()->format('Y-m-d'),
        ]);
    }

    public function test_driver_can_board_student_with_valid_qr_code(): void
    {
        $driver = $this->createDriver();
        $student = $this->createStudent();
        $trip = $this->createActiveTrip($driver);
        $stop = Stop::factory()->create(['route_id' => $trip->route_id]);

        Sanctum::actingAs($driver);

        $response = $this->postJson('/api/v1/boardings/board', [
            'trip_id' => $trip->id,
            'student_id' => $student->id,
            'stop_id' => $stop->id,
            'qrcode_token' => $student->qrcode_token,
        ]);

        $response->assertCreated()
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'trip_id',
                    'student_id',
                    'boarding_timestamp',
                    'landed_at',
                    'stop_id',
                ],
            ]);

        $this->assertDatabaseHas('boardings', [
            'trip_id' => $trip->id,
            'student_id' => $student->id,
            'stop_id' => $stop->id,
        ]);

        // Verifica se um novo token foi gerado
        $student->refresh();
        $this->assertNotEquals($student->qrcode_token, $response->json('data.qrcode_token'));
    }

    public function test_boarding_fails_with_invalid_qr_code(): void
    {
        $driver = $this->createDriver();
        $student = $this->createStudent();
        $trip = $this->createActiveTrip($driver);
        $stop = Stop::factory()->create(['route_id' => $trip->route_id]);

        Sanctum::actingAs($driver);

        $response = $this->postJson('/api/v1/boardings/board', [
            'trip_id' => $trip->id,
            'student_id' => $student->id,
            'stop_id' => $stop->id,
            'qrcode_token' => 'invalid-token',
        ]);

        $response->assertStatus(400)
            ->assertJson([
                'message' => 'Token de QR Code inválido.',
            ]);
    }

    public function test_boarding_fails_if_student_not_unboarded_from_previous_trip(): void
    {
        $driver = $this->createDriver();
        $student = $this->createStudent();
        $trip = $this->createActiveTrip($driver);
        $stop = Stop::factory()->create(['route_id' => $trip->route_id]);

        // Cria um embarque ativo anterior
        Boarding::factory()->create([
            'student_id' => $student->id,
            'trip_id' => $trip->id,
            'stop_id' => $stop->id,
            'landed_at' => null,
        ]);

        Sanctum::actingAs($driver);

        $response = $this->postJson('/api/v1/boardings/board', [
            'trip_id' => $trip->id,
            'student_id' => $student->id,
            'stop_id' => $stop->id,
            'qrcode_token' => $student->qrcode_token,
        ]);

        $response->assertStatus(400)
            ->assertJson([
                'message' => 'O estudante ainda não desembarcou da viagem anterior.',
            ]);
    }

    public function test_only_trip_driver_can_board_students(): void
    {
        $driver = $this->createDriver();
        $anotherDriver = $this->createDriver();
        $student = $this->createStudent();
        $trip = $this->createActiveTrip($driver);
        $stop = Stop::factory()->create(['route_id' => $trip->route_id]);

        Sanctum::actingAs($anotherDriver);

        $response = $this->postJson('/api/v1/boardings/board', [
            'trip_id' => $trip->id,
            'student_id' => $student->id,
            'stop_id' => $stop->id,
            'qrcode_token' => $student->qrcode_token,
        ]);

        $response->assertStatus(400)
            ->assertJson([
                'message' => 'Apenas o motorista responsável pela viagem pode autorizar embarques.',
            ]);
    }

    public function test_driver_can_unboard_any_student(): void
    {
        $driver = $this->createDriver();
        $student = $this->createStudent();
        $trip = $this->createActiveTrip($driver);
        $stop = Stop::factory()->create(['route_id' => $trip->route_id]);

        $boarding = Boarding::factory()->create([
            'trip_id' => $trip->id,
            'student_id' => $student->id,
            'stop_id' => $stop->id,
            'landed_at' => null,
        ]);

        Sanctum::actingAs($driver);

        $response = $this->postJson('/api/v1/boardings/unboard', [
            'trip_id' => $trip->id,
            'student_id' => $student->id,
        ]);

        $response->assertOk();

        $boarding->refresh();
        $this->assertNotNull($boarding->landed_at);
    }

    public function test_student_can_unboard_themselves(): void
    {
        $driver = $this->createDriver();
        $student = $this->createStudent();
        $trip = $this->createActiveTrip($driver);
        $stop = Stop::factory()->create(['route_id' => $trip->route_id]);

        $boarding = Boarding::factory()->create([
            'trip_id' => $trip->id,
            'student_id' => $student->id,
            'stop_id' => $stop->id,
            'landed_at' => null,
        ]);

        Sanctum::actingAs($student->user);

        $response = $this->postJson('/api/v1/boardings/unboard', [
            'trip_id' => $trip->id,
            'student_id' => $student->id,
        ]);

        $response->assertOk();

        $boarding->refresh();
        $this->assertNotNull($boarding->landed_at);
    }

    public function test_student_cannot_unboard_another_student(): void
    {
        $driver = $this->createDriver();
        $student1 = $this->createStudent();
        $student2 = $this->createStudent();
        $trip = $this->createActiveTrip($driver);
        $stop = Stop::factory()->create(['route_id' => $trip->route_id]);

        Boarding::factory()->create([
            'trip_id' => $trip->id,
            'student_id' => $student2->id,
            'stop_id' => $stop->id,
            'landed_at' => null,
        ]);

        Sanctum::actingAs($student1->user);

        $response = $this->postJson('/api/v1/boardings/unboard', [
            'trip_id' => $trip->id,
            'student_id' => $student2->id,
        ]);

        $response->assertStatus(400)
            ->assertJson([
                'message' => 'Você não tem permissão para realizar este desembarque.',
            ]);
    }

    public function test_completing_trip_unboards_all_students(): void
    {
        $driver = $this->createDriver();
        $trip = $this->createActiveTrip($driver);
        $stop = Stop::factory()->create(['route_id' => $trip->route_id]);

        $student1 = $this->createStudent();
        $student2 = $this->createStudent();

        Boarding::factory()->create([
            'trip_id' => $trip->id,
            'student_id' => $student1->id,
            'stop_id' => $stop->id,
            'landed_at' => null,
        ]);

        Boarding::factory()->create([
            'trip_id' => $trip->id,
            'student_id' => $student2->id,
            'stop_id' => $stop->id,
            'landed_at' => null,
        ]);

        Sanctum::actingAs($driver);

        $response = $this->patchJson("/api/v1/trips/{$trip->id}/complete");

        $response->assertOk();

        // Verifica que todos os embarques foram desembarcados
        $this->assertDatabaseMissing('boardings', [
            'trip_id' => $trip->id,
            'landed_at' => null,
        ]);
    }

    public function test_boarding_fails_if_trip_is_not_in_progress(): void
    {
        $driver = $this->createDriver();
        $student = $this->createStudent();
        $route = Route::factory()->create();
        $vehicle = Vehicle::factory()->create();
        $stop = Stop::factory()->create(['route_id' => $route->id]);

        $trip = Trip::factory()->create([
            'route_id' => $route->id,
            'driver_id' => $driver->id,
            'vehicle_id' => $vehicle->id,
            'status' => TripStatus::Scheduled,
            'trip_date' => now()->format('Y-m-d'),
        ]);

        Sanctum::actingAs($driver);

        $response = $this->postJson('/api/v1/boardings/board', [
            'trip_id' => $trip->id,
            'student_id' => $student->id,
            'stop_id' => $stop->id,
            'qrcode_token' => $student->qrcode_token,
        ]);

        $response->assertStatus(400)
            ->assertJson([
                'message' => 'A viagem não está em progresso.',
            ]);
    }

    public function test_unboarding_fails_if_student_is_not_boarded(): void
    {
        $driver = $this->createDriver();
        $student = $this->createStudent();
        $trip = $this->createActiveTrip($driver);

        Sanctum::actingAs($driver);

        $response = $this->postJson('/api/v1/boardings/unboard', [
            'trip_id' => $trip->id,
            'student_id' => $student->id,
        ]);

        $response->assertStatus(400)
            ->assertJson([
                'message' => 'Estudante não está embarcado nesta viagem.',
            ]);
    }

    public function test_can_list_all_passengers_of_a_trip(): void
    {
        $driver = $this->createDriver();
        $trip = $this->createActiveTrip($driver);
        $stop = Stop::factory()->create(['route_id' => $trip->route_id]);

        // create 3 students, 2 boarded and 1 landed
        $student1 = $this->createStudent();
        $student2 = $this->createStudent();
        $student3 = $this->createStudent();

        Boarding::factory()->create([
            'trip_id' => $trip->id,
            'student_id' => $student1->id,
            'stop_id' => $stop->id,
            'landed_at' => null, // Still boarded
        ]);

        Boarding::factory()->create([
            'trip_id' => $trip->id,
            'student_id' => $student2->id,
            'stop_id' => $stop->id,
            'landed_at' => null, // Still boarded
        ]);

        Boarding::factory()->create([
            'trip_id' => $trip->id,
            'student_id' => $student3->id,
            'stop_id' => $stop->id,
            'landed_at' => now(), // Landed
        ]);

        Sanctum::actingAs($driver);

        $response = $this->getJson("/api/v1/trips/{$trip->id}/passengers");

        $response->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'boarding_timestamp',
                        'landed_at',
                        'is_boarded',
                        'student',
                        'stop',
                    ],
                ],
            ]);
    }

    public function test_can_filter_only_boarded_passengers(): void
    {
        $driver = $this->createDriver();
        $trip = $this->createActiveTrip($driver);
        $stop = Stop::factory()->create(['route_id' => $trip->route_id]);

        // Create 2 boarded students and 1 landed
        $student1 = $this->createStudent();
        $student2 = $this->createStudent();
        $student3 = $this->createStudent();

        Boarding::factory()->create([
            'trip_id' => $trip->id,
            'student_id' => $student1->id,
            'stop_id' => $stop->id,
            'landed_at' => null,
        ]);

        Boarding::factory()->create([
            'trip_id' => $trip->id,
            'student_id' => $student2->id,
            'stop_id' => $stop->id,
            'landed_at' => null,
        ]);

        Boarding::factory()->create([
            'trip_id' => $trip->id,
            'student_id' => $student3->id,
            'stop_id' => $stop->id,
            'landed_at' => now(),
        ]);

        Sanctum::actingAs($driver);

        $response = $this->getJson("/api/v1/trips/{$trip->id}/passengers?only_boarded=true");

        $response->assertOk()
            ->assertJsonCount(2, 'data');

        $passengers = $response->json('data');

        foreach ($passengers as $passenger) {
            $this->assertTrue($passenger['is_boarded']);
            $this->assertNull($passenger['landed_at']);
        }
    }

    public function test_can_filter_only_landed_passengers(): void
    {
        $driver = $this->createDriver();
        $trip = $this->createActiveTrip($driver);
        $stop = Stop::factory()->create(['route_id' => $trip->route_id]);

        // Cria 2 estudantes embarcados e 2 desembarcados
        $student1 = $this->createStudent();
        $student2 = $this->createStudent();
        $student3 = $this->createStudent();
        $student4 = $this->createStudent();

        Boarding::factory()->create([
            'trip_id' => $trip->id,
            'student_id' => $student1->id,
            'stop_id' => $stop->id,
            'landed_at' => null,
        ]);

        Boarding::factory()->create([
            'trip_id' => $trip->id,
            'student_id' => $student2->id,
            'stop_id' => $stop->id,
            'landed_at' => null,
        ]);

        Boarding::factory()->create([
            'trip_id' => $trip->id,
            'student_id' => $student3->id,
            'stop_id' => $stop->id,
            'landed_at' => now(),
        ]);

        Boarding::factory()->create([
            'trip_id' => $trip->id,
            'student_id' => $student4->id,
            'stop_id' => $stop->id,
            'landed_at' => now(),
        ]);

        Sanctum::actingAs($driver);

        $response = $this->getJson("/api/v1/trips/{$trip->id}/passengers?only_boarded=false");

        $response->assertOk()
            ->assertJsonCount(2, 'data');

        $passengers = $response->json('data');

        foreach ($passengers as $passenger) {
            $this->assertFalse($passenger['is_boarded']);
            $this->assertNotNull($passenger['landed_at']);
        }
    }

    public function test_passengers_list_fails_for_non_existent_trip(): void
    {
        $driver = $this->createDriver();
        Sanctum::actingAs($driver);

        $response = $this->getJson('/api/v1/trips/99999/passengers');

        $response->assertStatus(400)
            ->assertJson([
                'message' => 'Viagem não encontrada.',
            ]);
    }

    public function test_passengers_list_requires_authentication(): void
    {
        $driver = $this->createDriver();
        $trip = $this->createActiveTrip($driver);

        $response = $this->getJson("/api/v1/trips/{$trip->id}/passengers");

        $response->assertUnauthorized();
    }
}
