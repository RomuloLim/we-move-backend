<?php

namespace Modules\Operation\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Operation\Enums\{AtuationForm, RequisitionStatus};
use Modules\Operation\Models\{Course, Institution, StudentRequisition};
use Modules\User\Enums\UserType;
use Modules\User\Models\User;

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
            'student_id' => User::factory()->create(['user_type' => UserType::Student->value])->id,
            'semester' => $this->faker->numberBetween(1, 10),
            'protocol' => 'REQ-' . date('Y') . '-' . strtoupper($this->faker->unique()->bothify('????????')),
            'status' => RequisitionStatus::Pending,
            'street_name' => $this->faker->streetName(),
            'house_number' => $this->faker->buildingNumber(),
            'neighborhood' => $this->faker->citySuffix(),
            'city' => $this->faker->city(),
            'phone_contact' => $this->faker->phoneNumber(),
            'birth_date' => $this->faker->date('Y-m-d', '-18 years'),
            'institution_email' => $this->faker->unique()->safeEmail(),
            'institution_registration' => $this->faker->unique()->numerify('########'),
            'institution_id' => Institution::factory()->create()->id,
            'course_id' => Course::factory()->create()->id,
            'atuation_form' => $this->faker->randomElement(AtuationForm::cases()),
            'deny_reason' => null,
        ];
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
}
