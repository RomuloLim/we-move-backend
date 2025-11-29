<?php

namespace Modules\Operation\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Modules\Logistics\Enums\TripStatus;
use Modules\Logistics\Models\{Route, Stop, Trip, Vehicle};
use Modules\Operation\Enums\RequisitionStatus;
use Modules\Operation\Models\{Course, Institution, Student, StudentRequisition};
use Modules\User\Enums\UserType;
use Modules\User\Models\User;
use Tests\TestCase;

class StudentDataTest extends TestCase
{
    use RefreshDatabase;

    private function createStudent(): Student
    {
        $user = User::factory()->create(['user_type' => UserType::Student]);

        return Student::factory()->create([
            'user_id' => $user->id,
        ]);
    }

    private function createDriver(): User
    {
        return User::factory()->create(['user_type' => UserType::Driver]);
    }

    private function createActiveTrip(): Trip
    {
        $driver = $this->createDriver();
        $vehicle = Vehicle::factory()->create();
        $route = Route::factory()->create();

        // Create stops for the route
        Stop::factory()->create([
            'route_id' => $route->id,
            'stop_name' => 'Parada 1',
            'order' => 1,
        ]);

        Stop::factory()->create([
            'route_id' => $route->id,
            'stop_name' => 'Parada 2',
            'order' => 2,
        ]);

        return Trip::factory()->create([
            'driver_id' => $driver->id,
            'vehicle_id' => $vehicle->id,
            'route_id' => $route->id,
            'status' => TripStatus::InProgress,
        ]);
    }

    public function test_can_get_student_full_data(): void
    {
        $student = $this->createStudent();
        $admin = User::factory()->create(['user_type' => UserType::Admin]);
        Sanctum::actingAs($admin);

        // Create institution course and requisition
        $institution = Institution::factory()->create();
        $course = Course::factory()->create();
        $institution->courses()->attach($course->id);
        $institutionCourseId = $institution->courses()->first()->pivot->id;

        StudentRequisition::factory()->create([
            'student_id' => $student->user_id,
            'institution_course_id' => $institutionCourseId,
            'status' => RequisitionStatus::Approved,
        ]);

        // Create active trip
        $this->createActiveTrip();

        $response = $this->getJson("/api/v1/students/{$student->id}");

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                'id',
                'user_id',
                'institution_course_id',
                'city_of_origin',
                'status',
                'qrcode_token',
                'user' => [
                    'id',
                    'name',
                    'email',
                ],
                'latest_requisition' => [
                    'id',
                    'protocol',
                    'status',
                ],
                'available_trips' => [
                    '*' => [
                        'id',
                        'route_id',
                        'driver_id',
                        'vehicle_id',
                        'status',
                        'route' => [
                            'id',
                            'route_name',
                            'stops' => [
                                '*' => [
                                    'id',
                                    'stop_name',
                                    'latitude',
                                    'longitude',
                                    'order',
                                ],
                            ],
                        ],
                    ],
                ],
                'created_at',
                'updated_at',
            ],
        ]);

        $response->assertJsonPath('data.id', $student->id);
        $response->assertJsonPath('data.user.id', $student->user_id);
        $this->assertNotEmpty($response->json('data.latest_requisition'));
        $this->assertNotEmpty($response->json('data.available_trips'));
    }

    public function test_returns_empty_available_trips_when_no_active_trips(): void
    {
        $student = $this->createStudent();
        $admin = User::factory()->create(['user_type' => UserType::Admin]);
        Sanctum::actingAs($admin);

        $response = $this->getJson("/api/v1/students/{$student->id}");

        $response->assertOk();
        $response->assertJsonPath('data.available_trips', []);
    }

    public function test_returns_null_latest_requisition_when_no_requisitions(): void
    {
        $student = $this->createStudent();
        $admin = User::factory()->create(['user_type' => UserType::Admin]);
        Sanctum::actingAs($admin);

        $response = $this->getJson("/api/v1/students/{$student->id}");

        $response->assertOk();
        $this->assertNull($response->json('data.latest_requisition'));
    }

    public function test_returns_only_latest_requisition(): void
    {
        $student = $this->createStudent();
        $admin = User::factory()->create(['user_type' => UserType::Admin]);
        Sanctum::actingAs($admin);

        $institution = Institution::factory()->create();
        $course = Course::factory()->create();
        $institution->courses()->attach($course->id);
        $institutionCourseId = $institution->courses()->first()->pivot->id;

        // Update student with institution course
        $student->update(['institution_course_id' => $institutionCourseId]);

        // Create multiple requisitions - student_id is actually the user_id
        StudentRequisition::factory()->create([
            'student_id' => $student->user_id,
            'institution_course_id' => $institutionCourseId,
            'protocol' => 'OLD-123',
            'created_at' => now()->subDays(10),
        ]);

        $latestRequisition = StudentRequisition::factory()->create([
            'student_id' => $student->user_id,
            'institution_course_id' => $institutionCourseId,
            'protocol' => 'NEW-456',
            'created_at' => now(),
        ]);

        $response = $this->getJson("/api/v1/students/{$student->id}");

        $response->assertOk();
        $response->assertJsonPath('data.latest_requisition.protocol', $latestRequisition->protocol);
    }

    public function test_returns_only_in_progress_trips(): void
    {
        $student = $this->createStudent();
        $admin = User::factory()->create(['user_type' => UserType::Admin]);
        Sanctum::actingAs($admin);

        // Create trips with different statuses
        $this->createActiveTrip(); // In progress

        $driver = $this->createDriver();
        $vehicle = Vehicle::factory()->create();
        $route2 = Route::factory()->create();

        Trip::factory()->create([
            'driver_id' => $driver->id,
            'vehicle_id' => $vehicle->id,
            'route_id' => $route2->id,
            'trip_date' => now()->addDay(),
            'status' => TripStatus::Scheduled,
        ]);

        $route3 = Route::factory()->create();
        Trip::factory()->create([
            'driver_id' => $driver->id,
            'vehicle_id' => $vehicle->id,
            'route_id' => $route3->id,
            'trip_date' => now()->addDays(2),
            'status' => TripStatus::Completed,
        ]);

        $response = $this->getJson("/api/v1/students/{$student->id}");

        $response->assertOk();
        $availableTrips = $response->json('data.available_trips');
        $this->assertCount(1, $availableTrips);
        $this->assertEquals(TripStatus::InProgress->value, $availableTrips[0]['status']);
    }

    public function test_returns_404_when_student_not_found(): void
    {
        $admin = User::factory()->create(['user_type' => UserType::Admin]);
        Sanctum::actingAs($admin);

        $response = $this->getJson('/api/v1/students/99999');

        $response->assertNotFound();
        $response->assertJson([
            'message' => 'Estudante não encontrado.',
        ]);
    }

    public function test_requires_authentication(): void
    {
        $student = $this->createStudent();

        $response = $this->getJson("/api/v1/students/{$student->id}");

        $response->assertUnauthorized();
    }

    public function test_student_can_access_own_data(): void
    {
        $student = $this->createStudent();
        $user = User::find($student->user_id);
        Sanctum::actingAs($user);

        $response = $this->getJson("/api/v1/students/{$student->id}");

        $response->assertOk();
        $response->assertJsonPath('data.id', $student->id);
    }

    public function test_student_cannot_access_other_student_data(): void
    {
        $student1 = $this->createStudent();
        $student2 = $this->createStudent();
        $user1 = User::find($student1->user_id);
        Sanctum::actingAs($user1);

        $response = $this->getJson("/api/v1/students/{$student2->id}");

        $response->assertForbidden();
        $response->assertJson([
            'message' => 'Você não está autorizado a acessar os dados deste estudante.',
        ]);
    }

    public function test_admin_can_access_any_student_data(): void
    {
        $student = $this->createStudent();
        $admin = User::factory()->create(['user_type' => UserType::Admin]);
        Sanctum::actingAs($admin);

        $response = $this->getJson("/api/v1/students/{$student->id}");

        $response->assertOk();
        $response->assertJsonPath('data.id', $student->id);
    }

    public function test_super_admin_can_access_any_student_data(): void
    {
        $student = $this->createStudent();
        $superAdmin = User::factory()->create(['user_type' => UserType::SuperAdmin]);
        Sanctum::actingAs($superAdmin);

        $response = $this->getJson("/api/v1/students/{$student->id}");

        $response->assertOk();
        $response->assertJsonPath('data.id', $student->id);
    }
}
