<?php

namespace Modules\Operation\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Operation\Enums\{AtuationForm, RequisitionStatus};
use Modules\Operation\Models\{Course, Institution, StudentRequisition};

class StudentRequisitionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = StudentRequisition::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'semester' => $this->faker->numberBetween(1, 10),
            'protocol' => 'REQ-' . date('Y') . '-' . strtoupper($this->faker->unique()->bothify('????????')),
            'status' => RequisitionStatus::Pending,
            'street_name' => $this->faker->streetName(),
            'house_number' => $this->faker->buildingNumber(),
            'neighborhood' => $this->faker->citySuffix(),
            'student_id' => null,
            'city' => $this->faker->city(),
            'phone_contact' => $this->faker->phoneNumber(),
            'birth_date' => $this->faker->date('Y-m-d', '-18 years'),
            'institution_email' => $this->faker->unique()->safeEmail(),
            'institution_registration' => $this->faker->unique()->numerify('########'),
            'atuation_form' => $this->faker->randomElement(AtuationForm::cases()),
            'deny_reason' => null,
            'institution_course_id' => null,
        ];
    }

    /**
     * Configure the model factory.
     */
    public function configure(): static
    {
        return $this->afterMaking(function (StudentRequisition $requisition) {
            // Generate institution course relationship if not provided
            if (!$requisition->institution_course_id) {
                $requisition->institution_course_id = $this->generateInstitutionCourseId();
            }

            // Validate that student_id is provided
            if (!$requisition->student_id) {
                throw new \InvalidArgumentException(
                    'StudentRequisitionFactory requires student_id to be explicitly provided. ' .
                    'Create a Student first and pass its ID: StudentRequisition::factory()->create([\'student_id\' => $student->id])'
                );
            }
        });
    }

    /**
     * Indicate that the requisition is approved.
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => RequisitionStatus::Approved,
        ]);
    }

    /**
     * Indicate that the requisition is reproved.
     */
    public function reproved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => RequisitionStatus::Reproved,
            'deny_reason' => $this->faker->sentence(),
        ]);
    }

    /**
     * Indicate that the requisition is expired.
     */
    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => RequisitionStatus::Expired,
        ]);
    }

    private function generateInstitutionCourseId(): int
    {
        $institution = Institution::factory()->create();
        $course = Course::factory()->create();
        $institution->courses()->attach($course->id);

        return $institution->courses()->first()->pivot->id;
    }
}
