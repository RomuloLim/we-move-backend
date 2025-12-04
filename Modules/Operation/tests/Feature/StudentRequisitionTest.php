<?php

namespace Modules\Operation\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Modules\Operation\Enums\{AtuationForm, RequisitionStatus};
use Modules\Operation\Models\{Course, Institution, Student, StudentRequisition};
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
            'semester' => 5,
            'institution_course_id' => $institution->courses()->first()->pivot->id,
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
        Sanctum::actingAs($student->user);

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
        Sanctum::actingAs($student->user);

        $institution = Institution::factory()->create();
        $course = Course::factory()->create();

        $institution->courses()->attach($course->id);

        // Create an approved requisition
        StudentRequisition::factory()->create([
            'student_id' => $student->id,
            'status' => RequisitionStatus::Approved,
            'status' => RequisitionStatus::Approved,
            'institution_course_id' => $institution->courses()->first()->pivot->id,
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
        Sanctum::actingAs($student->user);

        $institution = Institution::factory()->create();
        $course = Course::factory()->create();

        $institution->courses()->attach($course->id);

        // Create a pending requisition
        $existingRequisition = StudentRequisition::factory()->create([
            'student_id' => $student->id,
            'status' => RequisitionStatus::Pending,
            'status' => RequisitionStatus::Pending,
            'institution_course_id' => $institution->courses()->first()->pivot->id,
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
        Sanctum::actingAs($student->user);

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
            'institution_registration',
            'semester',
            'institution_course_id',
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

        Sanctum::actingAs($student1->user);
        $data1 = $this->getValidRequisitionData();
        $response1 = $this->postJson('/api/v1/requisitions', $data1);
        $response1->assertCreated();
        $protocol1 = $response1->json('data.protocol');

        Sanctum::actingAs($student2->user);
        $data2 = $this->getValidRequisitionData();
        $response2 = $this->postJson('/api/v1/requisitions', $data2);
        $response2->assertCreated();
        $protocol2 = $response2->json('data.protocol');

        $this->assertNotEquals($protocol1, $protocol2);
    }

    public function test_can_list_requisitions_without_filters(): void
    {
        $admin = User::factory()->create(['user_type' => UserType::Admin->value]);
        Sanctum::actingAs($admin);

        $institution = Institution::factory()->create();
        $course = Course::factory()->create();

        $institution->courses()->attach($course->id);

        // Criar 3 estudantes com usuários
        for ($i = 0; $i < 3; $i++) {
            $student = $this->createStudent();
            StudentRequisition::factory()->create([
                'student_id' => $student->id,
                'institution_course_id' => $institution->courses()->first()->pivot->id,
            ]);
        }

        $response = $this->getJson('/api/v1/requisitions');

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'student_id',
                    'protocol',
                    'status',
                    'semester',
                    'street_name',
                    'house_number',
                    'neighborhood',
                    'city',
                    'phone_contact',
                    'birth_date',
                    'atuation_form',
                    'deny_reason',
                    'created_at',
                    'updated_at',
                ],
            ],
            'links',
            'meta',
        ]);

        $this->assertCount(3, $response->json('data'));
    }

    public function test_can_filter_requisitions_by_protocol(): void
    {
        $admin = User::factory()->create(['user_type' => UserType::Admin->value]);
        Sanctum::actingAs($admin);

        $institution = Institution::factory()->create();
        $course = Course::factory()->create();

        $institution->courses()->attach($course->id);

        $student1 = $this->createStudent();
        $student2 = $this->createStudent();

        StudentRequisition::factory()->create([
            'student_id' => $student1->id,
            'protocol' => 'TEST-001',
            'institution_course_id' => $institution->courses()->first()->pivot->id,
        ]);

        StudentRequisition::factory()->create([
            'student_id' => $student2->id,
            'protocol' => 'TEST-002',
            'institution_course_id' => $institution->courses()->first()->pivot->id,
        ]);

        $response = $this->getJson('/api/v1/requisitions?protocol=TEST-001');

        $response->assertOk();
        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals('TEST-001', $data[0]['protocol']);
    }

    public function test_can_filter_requisitions_by_status(): void
    {
        $admin = User::factory()->create(['user_type' => UserType::Admin->value]);
        Sanctum::actingAs($admin);

        $institution = Institution::factory()->create();
        $course = Course::factory()->create();

        $institution->courses()->attach($course->id);

        $student1 = $this->createStudent();
        $student2 = $this->createStudent();

        StudentRequisition::factory()->create([
            'student_id' => $student1->id,
            'status' => RequisitionStatus::Pending,
            'institution_course_id' => $institution->courses()->first()->pivot->id,
        ]);

        StudentRequisition::factory()->create([
            'student_id' => $student2->id,
            'status' => RequisitionStatus::Approved,
            'institution_course_id' => $institution->courses()->first()->pivot->id,
        ]);

        $response = $this->getJson('/api/v1/requisitions?status=' . RequisitionStatus::Approved->value);

        $response->assertOk();
        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals(RequisitionStatus::Approved->value, $data[0]['status']);
    }

    public function test_can_filter_requisitions_by_atuation_form(): void
    {
        $admin = User::factory()->create(['user_type' => UserType::Admin->value]);
        Sanctum::actingAs($admin);

        $institution = Institution::factory()->create();
        $course = Course::factory()->create();

        $institution->courses()->attach($course->id);

        $student1 = $this->createStudent();
        $student2 = $this->createStudent();

        StudentRequisition::factory()->create([
            'student_id' => $student1->id,
            'atuation_form' => AtuationForm::Student,
            'institution_course_id' => $institution->courses()->first()->pivot->id,
        ]);

        StudentRequisition::factory()->create([
            'student_id' => $student2->id,
            'atuation_form' => AtuationForm::Teacher,
            'institution_course_id' => $institution->courses()->first()->pivot->id,
        ]);

        $response = $this->getJson('/api/v1/requisitions?atuation_form=' . AtuationForm::Teacher->value);

        $response->assertOk();
        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals(AtuationForm::Teacher->value, $data[0]['atuation_form']);
    }

    public function test_can_filter_requisitions_with_multiple_filters(): void
    {
        $admin = User::factory()->create(['user_type' => UserType::Admin->value]);
        Sanctum::actingAs($admin);

        $institution = Institution::factory()->create();
        $course = Course::factory()->create();

        $institution->courses()->attach($course->id);

        $student1 = $this->createStudent();
        $student2 = $this->createStudent();

        StudentRequisition::factory()->create([
            'student_id' => $student1->id,
            'status' => RequisitionStatus::Pending,
            'institution_course_id' => $institution->courses()->first()->pivot->id,
            'atuation_form' => AtuationForm::Student,
        ]);

        StudentRequisition::factory()->create([
            'student_id' => $student2->id,
            'status' => RequisitionStatus::Approved,
            'institution_course_id' => $institution->courses()->first()->pivot->id,
            'atuation_form' => AtuationForm::Student,
        ]);

        $response = $this->getJson('/api/v1/requisitions?status=' . RequisitionStatus::Pending->value . '&atuation_form=' . AtuationForm::Student->value);

        $response->assertOk();
        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals(RequisitionStatus::Pending->value, $data[0]['status']);
        $this->assertEquals(AtuationForm::Student->value, $data[0]['atuation_form']);
    }

    public function test_requisition_list_returns_pagination_meta(): void
    {
        $admin = User::factory()->create(['user_type' => UserType::Admin->value]);
        Sanctum::actingAs($admin);

        $institution = Institution::factory()->create();
        $course = Course::factory()->create();

        $institution->courses()->attach($course->id);

        // Criar 20 estudantes com usuários
        for ($i = 0; $i < 20; $i++) {
            $student = $this->createStudent();
            StudentRequisition::factory()->create([
                'student_id' => $student->id,
                'institution_course_id' => $institution->courses()->first()->pivot->id,
            ]);
        }

        $response = $this->getJson('/api/v1/requisitions');

        $response->assertOk();
        $response->assertJsonStructure([
            'data',
            'links' => ['first', 'last', 'prev', 'next'],
            'meta' => [
                'current_page',
                'from',
                'last_page',
                'per_page',
                'to',
                'total',
            ],
        ]);

        $meta = $response->json('meta');
        $this->assertEquals(20, $meta['total']);
        $this->assertGreaterThan(1, $meta['last_page']);
    }

    public function test_requisition_list_validates_invalid_status_enum(): void
    {
        $admin = User::factory()->create(['user_type' => UserType::Admin->value]);
        Sanctum::actingAs($admin);

        $response = $this->getJson('/api/v1/requisitions?status=invalid_status');

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['status']);
    }

    public function test_requisition_list_validates_invalid_atuation_form_enum(): void
    {
        $admin = User::factory()->create(['user_type' => UserType::Admin->value]);
        Sanctum::actingAs($admin);

        $response = $this->getJson('/api/v1/requisitions?atuation_form=invalid_form');

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['atuation_form']);
    }

    public function test_unauthenticated_user_cannot_list_requisitions(): void
    {
        $response = $this->getJson('/api/v1/requisitions');

        $response->assertUnauthorized();
    }

    public function test_requisition_list_includes_institution_when_loaded(): void
    {
        $admin = User::factory()->create(['user_type' => UserType::Admin->value]);
        Sanctum::actingAs($admin);

        $institution = Institution::factory()->create(['name' => 'Test University']);
        $course = Course::factory()->create();

        $institution->courses()->attach($course->id);

        $student = $this->createStudent();

        StudentRequisition::factory()->create([
            'student_id' => $student->id,
            'institution_course_id' => $institution->courses()->first()->pivot->id,
        ]);

        $response = $this->getJson('/api/v1/requisitions');

        $response->assertOk();
        $data = $response->json('data');

        if (isset($data[0]['institution'])) {
            $this->assertEquals('Test University', $data[0]['institution']['name']);
        }
    }

    public function test_requisition_list_includes_course_when_loaded(): void
    {
        $admin = User::factory()->create(['user_type' => UserType::Admin->value]);
        Sanctum::actingAs($admin);

        $institution = Institution::factory()->create();
        $course = Course::factory()->create(['name' => 'Computer Science']);

        $institution->courses()->attach($course->id);

        $student = $this->createStudent();

        StudentRequisition::factory()->create([
            'student_id' => $student->id,
            'institution_course_id' => $institution->courses()->first()->pivot->id,
        ]);

        $response = $this->getJson('/api/v1/requisitions');

        $response->assertOk();
        $data = $response->json('data');

        if (isset($data[0]['course'])) {
            $this->assertEquals('Computer Science', $data[0]['course']['name']);
        }
    }
}
