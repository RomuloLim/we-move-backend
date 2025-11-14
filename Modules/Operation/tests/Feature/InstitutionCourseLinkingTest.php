<?php

namespace Modules\Operation\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Modules\Operation\Models\{Course, Institution};
use Modules\User\Enums\UserType;
use Modules\User\Models\User;
use Tests\TestCase;

class InstitutionCourseLinkingTest extends TestCase
{
    use RefreshDatabase;

    private function userActingAs(UserType $userType): void
    {
        $user = User::factory()->create(['user_type' => $userType->value]);

        Sanctum::actingAs($user);
    }

    public function test_admin_can_link_course_to_institution(): void
    {
        $this->userActingAs(UserType::Admin);

        $institution = Institution::factory()->create();
        $courses = Course::factory()->count(3)->create();
        $coursesIds = $courses->pluck('id')->toArray();

        $response = $this->postJson("/api/v1/institutions/{$institution->id}/courses", [
            'courses_ids' => $coursesIds,
        ]);

        $response->assertCreated();

        foreach ($coursesIds as $courseId) {
            $this->assertDatabaseHas('institution_courses', [
                'institution_id' => $institution->id,
                'course_id' => $courseId,
            ]);
        }
    }

    public function test_admin_can_unlink_course_from_institution(): void
    {
        $this->userActingAs(UserType::Admin);

        $institution = Institution::factory()->create();
        $courses = Course::factory()->count(3)->create();
        $coursesIds = $courses->pluck('id')->toArray();

        $institution->courses()->attach($coursesIds);

        $response = $this->deleteJson("/api/v1/institutions/{$institution->id}/courses/unlink", [
            'courses_ids' => $coursesIds,
        ]);

        $response->assertOk();

        foreach ($coursesIds as $courseId) {
            $this->assertDatabaseMissing('institution_courses', [
                'institution_id' => $institution->id,
                'course_id' => $courseId,
            ]);
        }
    }

    public function test_student_cannot_link_course_to_institution(): void
    {
        $this->userActingAs(UserType::Student);

        $institution = Institution::factory()->create();
        $courses = Course::factory()->count(2)->create();
        $coursesIds = $courses->pluck('id')->toArray();

        $response = $this->postJson("/api/v1/institutions/{$institution->id}/courses", [
            'courses_ids' => $coursesIds,
        ]);

        $response->assertForbidden();
    }

    public function test_linking_same_course_twice_returns_existing_link(): void
    {
        $this->userActingAs(UserType::Admin);

        $institution = Institution::factory()->create();
        $courses = Course::factory()->count(2)->create();
        $coursesIds = $courses->pluck('id')->toArray();

        $this->postJson("/api/v1/institutions/{$institution->id}/courses", [
            'courses_ids' => $coursesIds,
        ])->assertCreated();

        $response = $this->postJson("/api/v1/institutions/{$institution->id}/courses", [
            'courses_ids' => $coursesIds,
        ]);

        $response->assertCreated();

        foreach ($coursesIds as $courseId) {
            $this->assertDatabaseHas('institution_courses', [
                'institution_id' => $institution->id,
                'course_id' => $courseId,
            ]);
        }
    }

    public function test_unlinking_non_existent_link_returns_not_found(): void
    {
        $this->userActingAs(UserType::Admin);

        $institution = Institution::factory()->create();
        $courses = Course::factory()->count(2)->create();
        $coursesIds = $courses->pluck('id')->toArray();

        $response = $this->deleteJson("/api/v1/institutions/{$institution->id}/courses/unlink", [
            'courses_ids' => $coursesIds,
        ]);

        $response->assertNotFound();
    }
}
