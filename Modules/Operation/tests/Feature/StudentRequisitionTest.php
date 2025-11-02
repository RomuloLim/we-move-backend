<?php

namespace Modules\Operation\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Modules\Operation\Enums\{AtuationForm, RequisitionStatus};
use Modules\Operation\Models\{Course, Institution, StudentRequisition};
use Modules\User\Enums\UserType;
use Modules\User\Models\User;
use Tests\TestCase;

class StudentRequisitionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    private function createStudent(): User
    {
        return User::factory()->create(['user_type' => UserType::Student->value]);
    }

    private function getValidRequisitionData(): array
    {
        $institution = Institution::factory()->create();
        $course = Course::factory()->create();
        $institution->courses()->attach($course->id);

        return [
            'street_name' => 'Rua Teste',
            'house_number' => '123',
            'neighborhood' => 'Centro',
            'city' => 'São Paulo',
            'phone_contact' => '11999999999',
            'birth_date' => '2000-01-01',
            'institution_email' => 'student@university.edu.br',
            'institution_registration' => '202401234',
            'semester' => 5,
            'institution_id' => $institution->id,
            'course_id' => $course->id,
            'atuation_form' => AtuationForm::Student->value,
            'residency_proof' => UploadedFile::fake()->create('residency.pdf', 1024),
            'identification_document' => UploadedFile::fake()->create('id.pdf', 1024),
            'profile_picture' => UploadedFile::fake()->image('photo.jpg'),
            'enrollment_proof' => UploadedFile::fake()->create('enrollment.pdf', 1024),
        ];
    }

    public function test_student_can_submit_requisition(): void
    {
        $student = $this->createStudent();
        Sanctum::actingAs($student);

        $data = $this->getValidRequisitionData();

        $response = $this->postJson('/api/v1/requisitions', $data);

        $response->assertCreated();
        $response->assertJsonStructure([
            'message',
            'data' => ['protocol', 'status'],
        ]);

        $this->assertDatabaseHas('student_requisitions', [
            'student_id' => $student->id,
            'status' => RequisitionStatus::Pending->value,
        ]);

        // Verify documents were created
        $this->assertDatabaseCount('documents', 4);
    }

    public function test_student_cannot_submit_requisition_if_already_approved(): void
    {
        $student = $this->createStudent();
        Sanctum::actingAs($student);

        $institution = Institution::factory()->create();
        $course = Course::factory()->create();

        // Create an approved requisition
        StudentRequisition::factory()->create([
            'student_id' => $student->id,
            'status' => RequisitionStatus::Approved,
            'institution_id' => $institution->id,
            'course_id' => $course->id,
        ]);

        $data = $this->getValidRequisitionData();

        $response = $this->postJson('/api/v1/requisitions', $data);

        $response->assertForbidden();
        $response->assertJson([
            'message' => 'Você já possui uma solicitação aprovada e não pode enviar outra.',
        ]);
    }

    public function test_student_can_update_pending_requisition(): void
    {
        $student = $this->createStudent();
        Sanctum::actingAs($student);

        $institution = Institution::factory()->create();
        $course = Course::factory()->create();

        // Create a pending requisition
        $existingRequisition = StudentRequisition::factory()->create([
            'student_id' => $student->id,
            'status' => RequisitionStatus::Pending,
            'institution_id' => $institution->id,
            'course_id' => $course->id,
            'semester' => 3,
        ]);

        $data = $this->getValidRequisitionData();
        $data['semester'] = 4;

        $response = $this->postJson('/api/v1/requisitions', $data);

        $response->assertCreated();

        // Verify the existing requisition was updated
        $existingRequisition->refresh();
        $this->assertEquals(4, $existingRequisition->semester);

        // Verify only one requisition exists for this student
        $this->assertDatabaseCount('student_requisitions', 1);
    }

    public function test_non_student_cannot_submit_requisition(): void
    {
        $admin = User::factory()->create(['user_type' => UserType::Admin->value]);
        Sanctum::actingAs($admin);

        $data = $this->getValidRequisitionData();

        $response = $this->postJson('/api/v1/requisitions', $data);

        $response->assertForbidden();
    }

    public function test_unauthenticated_user_cannot_submit_requisition(): void
    {
        $data = $this->getValidRequisitionData();

        $response = $this->postJson('/api/v1/requisitions', $data);

        $response->assertUnauthorized();
    }

    public function test_requisition_requires_all_fields(): void
    {
        $student = $this->createStudent();
        Sanctum::actingAs($student);

        $response = $this->postJson('/api/v1/requisitions', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'street_name',
            'neighborhood',
            'city',
            'phone_contact',
            'birth_date',
            'institution_email',
            'institution_registration',
            'semester',
            'institution_id',
            'course_id',
            'atuation_form',
            'residency_proof',
            'identification_document',
            'profile_picture',
            'enrollment_proof',
        ]);
    }

    public function test_protocol_is_unique(): void
    {
        $student1 = $this->createStudent();
        $student2 = $this->createStudent();

        Sanctum::actingAs($student1);
        $data1 = $this->getValidRequisitionData();
        $response1 = $this->postJson('/api/v1/requisitions', $data1);
        $response1->assertCreated();
        $protocol1 = $response1->json('data.protocol');

        Sanctum::actingAs($student2);
        $data2 = $this->getValidRequisitionData();
        $response2 = $this->postJson('/api/v1/requisitions', $data2);
        $response2->assertCreated();
        $protocol2 = $response2->json('data.protocol');

        $this->assertNotEquals($protocol1, $protocol2);
    }
}
