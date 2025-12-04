<?php

namespace Modules\Operation\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Modules\Operation\Enums\{ReprovedFieldEnum, RequisitionStatus};
use Modules\Operation\Models\{Course, Institution, Student, StudentRequisition};
use Modules\User\Enums\UserType;
use Modules\User\Models\User;
use Tests\TestCase;

class StudentRequisitionApprovalTest extends TestCase
{
    use RefreshDatabase;

    private function createAdmin(): User
    {
        return User::factory()->create(['user_type' => UserType::Admin->value]);
    }

    private function createStudent(): Student
    {
        $user = User::factory()->create(['user_type' => UserType::Student->value]);
        $institution = Institution::factory()->create();
        $course = Course::factory()->create();
        $institution->courses()->attach($course->id);

        return Student::factory()->create([
            'user_id' => $user->id,
            'institution_course_id' => $institution->courses()->first()->pivot->id,
        ]);
    }

    private function createRequisition(Student $student): StudentRequisition
    {
        return StudentRequisition::factory()->create([
            'student_id' => $student->id,
            'status' => RequisitionStatus::Pending,
            'institution_course_id' => $student->institution_course_id,
        ]);
    }

    public function test_admin_can_approve_requisition(): void
    {
        $admin = $this->createAdmin();
        Sanctum::actingAs($admin);

        $student = $this->createStudent();
        $requisition = $this->createRequisition($student);

        $response = $this->patchJson("/api/v1/requisitions/{$requisition->id}/approve");

        $response->assertOk();
        $response->assertJsonPath('data.status', RequisitionStatus::Approved->value);

        $this->assertDatabaseHas('student_requisitions', [
            'id' => $requisition->id,
            'status' => RequisitionStatus::Approved->value,
        ]);

        $this->assertDatabaseHas('students', [
            'id' => $student->id,
            'status' => RequisitionStatus::Approved->value,
        ]);
    }

    public function test_admin_can_reprove_requisition(): void
    {
        $admin = $this->createAdmin();
        Sanctum::actingAs($admin);

        $student = $this->createStudent();
        $requisition = $this->createRequisition($student);

        $data = [
            'deny_reason' => 'Documentos ilegíveis',
            'reproved_fields' => [
                ReprovedFieldEnum::IdentificationDocument->value,
                ReprovedFieldEnum::ResidencyProof->value,
            ],
        ];

        $response = $this->patchJson("/api/v1/requisitions/{$requisition->id}/reprove", $data);

        $response->assertOk();
        $response->assertJsonPath('data.status', RequisitionStatus::Reproved->value);

        $this->assertDatabaseHas('student_requisitions', [
            'id' => $requisition->id,
            'status' => RequisitionStatus::Reproved->value,
            'deny_reason' => 'Documentos ilegíveis',
        ]);

        $requisition->refresh();
        $this->assertEquals($data['reproved_fields'], $requisition->reproved_fields);
    }

    public function test_student_cannot_approve_requisition(): void
    {
        $student = $this->createStudent();
        Sanctum::actingAs($student->user);

        $requisition = $this->createRequisition($student);

        $response = $this->patchJson("/api/v1/requisitions/{$requisition->id}/approve");

        $response->assertForbidden();
    }

    public function test_reprove_requires_fields_and_reason(): void
    {
        $admin = $this->createAdmin();
        Sanctum::actingAs($admin);

        $student = $this->createStudent();
        $requisition = $this->createRequisition($student);

        $response = $this->patchJson("/api/v1/requisitions/{$requisition->id}/reprove", []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['deny_reason', 'reproved_fields']);
    }

    public function test_reprove_validates_enum_fields(): void
    {
        $admin = $this->createAdmin();
        Sanctum::actingAs($admin);

        $student = $this->createStudent();
        $requisition = $this->createRequisition($student);

        $data = [
            'deny_reason' => 'Reason',
            'reproved_fields' => ['invalid_field'],
        ];

        $response = $this->patchJson("/api/v1/requisitions/{$requisition->id}/reprove", $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['reproved_fields.0']);
    }
}
