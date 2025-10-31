<?php

namespace Modules\Operation\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Modules\Operation\Models\Course;
use Modules\User\Enums\UserType;
use Modules\User\Models\User;
use Tests\TestCase;

class CourseCrudTest extends TestCase
{
    use RefreshDatabase;

    private function userActingAs(UserType $userType): void
    {
        $user = User::factory()->create(['user_type' => $userType->value]);

        Sanctum::actingAs($user);
    }

    public function test_admin_can_create_course(): void
    {
        $this->userActingAs(UserType::Admin);

        $data = ['name' => 'Ciência da Computação'];

        $response = $this->postJson('/api/v1/courses', $data);
        $response->assertCreated();
        $this->assertDatabaseHas('courses', $data);
    }

    public function test_admin_can_list_courses(): void
    {
        $this->userActingAs(UserType::Admin);

        Course::factory()->create(['name' => 'Test Course']);

        $response = $this->getJson('/api/v1/courses');
        $response->assertOk()->assertJsonFragment(['name' => 'Test Course']);
    }

    public function test_admin_can_show_course(): void
    {
        $this->userActingAs(UserType::Admin);

        $course = Course::factory()->create(['name' => 'Show Course']);

        $response = $this->getJson('/api/v1/courses/' . $course->id);
        $response->assertOk()->assertJsonFragment(['name' => 'Show Course']);
    }

    public function test_admin_can_update_course(): void
    {
        $this->userActingAs(UserType::Admin);

        $course = Course::factory()->create(['name' => 'Old Course']);
        $data = ['name' => 'New Course'];

        $response = $this->putJson('/api/v1/courses/' . $course->id, $data);
        $response->assertOk();
        $this->assertDatabaseHas('courses', $data);
    }

    public function test_admin_can_delete_course(): void
    {
        $this->userActingAs(UserType::Admin);

        $course = Course::factory()->create();

        $response = $this->deleteJson('/api/v1/courses/' . $course->id);
        $response->assertOk();
        $this->assertDatabaseMissing('courses', ['id' => $course->id]);
    }

    public function test_student_can_view_courses(): void
    {
        $this->userActingAs(UserType::Student);

        Course::factory()->create(['name' => 'Student View Course']);

        $response = $this->getJson('/api/v1/courses');
        $response->assertOk()->assertJsonFragment(['name' => 'Student View Course']);
    }

    public function test_student_cannot_create_course(): void
    {
        $this->userActingAs(UserType::Student);

        $data = ['name' => 'Unauthorized Course'];

        $response = $this->postJson('/api/v1/courses', $data);
        $response->assertForbidden();
    }
}
