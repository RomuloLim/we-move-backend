<?php

namespace Modules\Operation\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Modules\Operation\Enums\{AtuationForm, RequisitionStatus};
use Modules\Operation\Models\{Course, Institution};
use Modules\User\Enums\UserType;
use Modules\User\Models\User;
use Tests\TestCase;

class StudentRequisitionSyncTest extends TestCase
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
            'institution_course_id' => $institution->courses()->first()->pivot->id,
            'atuation_form' => AtuationForm::Student->value,
            'residency_proof' => UploadedFile::fake()->create('residency.pdf', 1024),
            'identification_document' => UploadedFile::fake()->create('id.pdf', 1024),
            'profile_picture' => UploadedFile::fake()->image('photo.jpg'),
            'enrollment_proof' => UploadedFile::fake()->create('enrollment.pdf', 1024),
        ];
    }

    public function test_student_record_is_created_when_requisition_is_submitted(): void
    {
        $student = $this->createStudent();
        Sanctum::actingAs($student);

        $data = $this->getValidRequisitionData();

        $response = $this->postJson('/api/v1/requisitions', $data);

        $response->assertCreated();

        $this->assertDatabaseHas('students', [
            'user_id' => $student->id,
            'city_of_origin' => 'São Paulo',
            'status' => RequisitionStatus::Pending->value,
        ]);
    }

    public function test_student_record_is_updated_when_requisition_is_updated(): void
    {
        $student = $this->createStudent();
        Sanctum::actingAs($student);

        $institution = Institution::factory()->create();
        $course = Course::factory()->create();
        $institution->courses()->attach($course->id);

        // Create initial requisition (which should create student)
        $data = $this->getValidRequisitionData();
        $data['city'] = 'Rio de Janeiro';

        $this->postJson('/api/v1/requisitions', $data);

        $this->assertDatabaseHas('students', [
            'user_id' => $student->id,
            'city_of_origin' => 'Rio de Janeiro',
        ]);

        // Update requisition
        $data['city'] = 'Curitiba';
        $data['semester'] = 6; // Change something else too

        $response = $this->postJson('/api/v1/requisitions', $data);

        $response->assertCreated(); // or whatever status update returns

        $this->assertDatabaseHas('students', [
            'user_id' => $student->id,
            'city_of_origin' => 'Curitiba',
        ]);

        // Ensure no duplicate student records
        $this->assertDatabaseCount('students', 1);
    }
}
