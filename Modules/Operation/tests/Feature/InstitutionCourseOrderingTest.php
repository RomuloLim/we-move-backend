<?php

namespace Modules\Operation\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Modules\Operation\Models\{Course, Institution};
use Modules\User\Enums\UserType;
use Modules\User\Models\User;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class InstitutionCourseOrderingTest extends TestCase
{
    use RefreshDatabase;

    private function userActingAs(UserType $userType): void
    {
        $user = User::factory()->create(['user_type' => $userType->value]);

        Sanctum::actingAs($user);
    }

    #[DataProvider('authorizedUserTypesProvider')]
    public function test_authorized_users_can_get_institutions_ordered_by_course(UserType $userType): void
    {
        $this->userActingAs($userType);

        $course = Course::factory()->create(['name' => 'Engineering']);
        $institutions = Institution::factory()->count(5)->create();

        $course->institutions()->attach($institutions->take(3)->pluck('id'));

        $response = $this->getJson("/api/v1/institutions/ordered-by-course/{$course->id}");

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'acronym',
                    'street',
                    'number',
                    'complement',
                    'neighborhood',
                    'city',
                    'state',
                    'zip_code',
                    'created_at',
                    'updated_at',
                ],
            ],
            'links',
            'meta',
        ]);
    }

    public function test_institutions_linked_to_course_appear_first_in_ordering(): void
    {
        $this->userActingAs(UserType::Admin);

        $course = Course::factory()->create(['name' => 'Engineering']);

        $linkedInstitution1 = Institution::factory()->create(['name' => 'Linked A']);
        $linkedInstitution2 = Institution::factory()->create(['name' => 'Linked B']);
        $linkedInstitution3 = Institution::factory()->create(['name' => 'Linked C']);

        $unlinkedInstitution1 = Institution::factory()->create(['name' => 'Unlinked A']);
        $unlinkedInstitution2 = Institution::factory()->create(['name' => 'Unlinked B']);

        $course->institutions()->attach([
            $linkedInstitution1->id,
            $linkedInstitution2->id,
            $linkedInstitution3->id,
        ]);

        $response = $this->getJson("/api/v1/institutions/ordered-by-course/{$course->id}");

        $response->assertOk();

        $linkedIds = [
            $linkedInstitution1->id,
            $linkedInstitution2->id,
            $linkedInstitution3->id,
        ];

        $unlinkedIds = [
            $unlinkedInstitution1->id,
            $unlinkedInstitution2->id,
        ];

        [$firstThreeIds, $lastTwoIds] = $this->separateIdsFromResponse($response);

        foreach ($firstThreeIds as $id) {
            $this->assertContains($id, $linkedIds, 'The first 3 institutions should be linked to the course');
        }

        foreach ($lastTwoIds as $id) {
            $this->assertContains($id, $unlinkedIds, 'The last 2 institutions should not be linked to the course');
        }
    }

    #[DataProvider('authorizedUserTypesProvider')]
    public function test_authorized_users_can_get_courses_ordered_by_institution(UserType $userType): void
    {
        $this->userActingAs($userType);

        $institution = Institution::factory()->create(['name' => 'Harvard University']);
        $courses = Course::factory()->count(5)->create();

        $institution->courses()->attach($courses->take(3)->pluck('id'));

        $response = $this->getJson("/api/v1/courses/ordered-by-institution/{$institution->id}");

        $response->assertOk();
        $response->assertJsonStructure([
            'data',
            'links',
            'meta',
        ]);
    }

    public function test_courses_linked_to_institution_appear_first_in_ordering(): void
    {
        $this->userActingAs(UserType::Admin);

        $institution = Institution::factory()->create(['name' => 'Harvard University']);

        $linkedCourse1 = Course::factory()->create(['name' => 'Linked Course A']);
        $linkedCourse2 = Course::factory()->create(['name' => 'Linked Course B']);
        $linkedCourse3 = Course::factory()->create(['name' => 'Linked Course C']);

        $unlinkedCourse1 = Course::factory()->create(['name' => 'Unlinked Course A']);
        $unlinkedCourse2 = Course::factory()->create(['name' => 'Unlinked Course B']);

        $institution->courses()->attach([
            $linkedCourse1->id,
            $linkedCourse2->id,
            $linkedCourse3->id,
        ]);

        $response = $this->getJson("/api/v1/courses/ordered-by-institution/{$institution->id}");

        $response->assertOk();

        [$firstThreeIds, $lastTwoIds] = $this->separateIdsFromResponse($response);

        $linkedIds = [
            $linkedCourse1->id,
            $linkedCourse2->id,
            $linkedCourse3->id,
        ];

        $unlinkedIds = [
            $unlinkedCourse1->id,
            $unlinkedCourse2->id,
        ];

        foreach ($firstThreeIds as $id) {
            $this->assertContains($id, $linkedIds, 'The first 3 courses should be linked to the institution');
        }

        foreach ($lastTwoIds as $id) {
            $this->assertContains($id, $unlinkedIds, 'The last 2 courses should not be linked to the institution');
        }
    }

    public static function authorizedUserTypesProvider(): \Generator
    {
        yield 'Admin user type' => [UserType::Admin];
        yield 'Student user type' => [UserType::Student];
    }

    public function test_get_institutions_ordered_by_nonexistent_course_returns_empty(): void
    {
        $this->userActingAs(UserType::Admin);

        $nonExistentCourseId = 999999;

        $response = $this->getJson("/api/v1/institutions/ordered-by-course/{$nonExistentCourseId}");

        $response->assertOk();
        $response->assertJsonStructure([
            'data',
            'links',
            'meta',
        ]);
    }

    public function test_get_courses_ordered_by_nonexistent_institution_returns_empty(): void
    {
        $this->userActingAs(UserType::Admin);

        $nonExistentInstitutionId = 999999;

        $response = $this->getJson("/api/v1/courses/ordered-by-institution/{$nonExistentInstitutionId}");

        $response->assertOk();
        $response->assertJsonStructure([
            'data',
            'links',
            'meta',
        ]);
    }

    public function test_unauthenticated_user_cannot_get_institutions_ordered_by_course(): void
    {
        $course = Course::factory()->create();

        $response = $this->getJson("/api/v1/institutions/ordered-by-course/{$course->id}");

        $response->assertUnauthorized();
    }

    public function test_unauthenticated_user_cannot_get_courses_ordered_by_institution(): void
    {
        $institution = Institution::factory()->create();

        $response = $this->getJson("/api/v1/courses/ordered-by-institution/{$institution->id}");

        $response->assertUnauthorized();
    }

    public function test_institutions_ordered_by_course_returns_paginated_data(): void
    {
        $this->userActingAs(UserType::Admin);

        $course = Course::factory()->create(['name' => 'Computer Science']);
        $institutions = Institution::factory()->count(20)->create();

        $course->institutions()->attach($institutions->pluck('id'));

        $response = $this->getJson("/api/v1/institutions/ordered-by-course/{$course->id}");

        $response->assertOk();
        $response->assertJsonStructure([
            'data',
            'links' => [
                'first',
                'last',
                'prev',
                'next',
            ],
            'meta' => [
                'current_page',
                'from',
                'last_page',
                'path',
                'per_page',
                'to',
                'total',
            ],
        ]);
    }

    public function test_courses_ordered_by_institution_returns_paginated_data(): void
    {
        $this->userActingAs(UserType::Admin);

        $institution = Institution::factory()->create(['name' => 'Stanford']);
        $courses = Course::factory()->count(20)->create();

        $institution->courses()->attach($courses->pluck('id'));

        $response = $this->getJson("/api/v1/courses/ordered-by-institution/{$institution->id}");

        $response->assertOk();
        $response->assertJsonStructure([
            'data',
            'links' => [
                'first',
                'last',
                'prev',
                'next',
            ],
            'meta' => [
                'current_page',
                'from',
                'last_page',
                'path',
                'per_page',
                'to',
                'total',
            ],
        ]);
    }

    private function separateIdsFromResponse(\Illuminate\Testing\TestResponse $response)
    {
        $reponseData = $response->json('data');

        $collectionData = collect($reponseData);

        $firstThreeIds = $collectionData
            ->take(3)
            ->pluck('id')
            ->toArray();

        $lastTwoIds = $collectionData
            ->skip(3)
            ->take(2)
            ->pluck('id')
            ->toArray();

        return [$firstThreeIds, $lastTwoIds];
    }
}

