<?php

namespace Modules\Operation\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Modules\Operation\Enums\CourseType;
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

        $data = [
            'name' => mb_strtoupper('Ciência da Computação', 'UTF-8'),
            'course_type' => CourseType::Graduate->value,
        ];

        $response = $this->postJson('/api/v1/courses', $data);
        $response->assertCreated();
        $this->assertDatabaseHas('courses', $data);
    }

    public function test_admin_can_list_courses(): void
    {
        $this->userActingAs(UserType::Admin);

        $courseName = 'Test Course';

        Course::factory()->create(['name' => $courseName]);

        $response = $this->getJson('/api/v1/courses');
        $response->assertOk()->assertJsonFragment(['name' => mb_strtoupper($courseName)]);
    }

    public function test_admin_can_show_course(): void
    {
        $this->userActingAs(UserType::Admin);

        $courseName = 'Show Course';

        $course = Course::factory()->create(['name' => $courseName]);

        $response = $this->getJson('/api/v1/courses/' . $course->id);
        $response->assertOk()->assertJsonFragment(['name' => mb_strtoupper($courseName)]);
    }

    public function test_admin_can_update_course(): void
    {
        $this->userActingAs(UserType::Admin);

        $course = Course::factory()->create(['name' => 'Old Course']);
        $data = [
            'name' => mb_strtoupper('Updated Course'),
            'course_type' => CourseType::Postgraduate->value,
        ];

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

        $courseName = 'Student View Course';

        Course::factory()->create(['name' => $courseName]);

        $response = $this->getJson('/api/v1/courses');
        $response->assertOk()->assertJsonFragment(['name' => mb_strtoupper($courseName)]);
    }

    public function test_student_cannot_create_course(): void
    {
        $this->userActingAs(UserType::Student);

        $data = ['name' => 'Unauthorized Course'];

        $response = $this->postJson('/api/v1/courses', $data);
        $response->assertForbidden();
    }
}
