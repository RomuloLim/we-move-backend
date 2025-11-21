<?php

namespace Modules\Operation\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Operation\Enums\RequisitionStatus;
use Modules\Operation\Models\InstitutionCourse;
use Modules\Operation\Models\Student;
use Modules\User\Models\User;

class StudentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = Student::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $statusCases = $this->faker->randomElement(RequisitionStatus::cases());
        return [
            'user_id' => User::factory(),
            'institution_course_id' => InstitutionCourse::factory(),
            'city_of_origin' => fake()->city(),
            'status' => $statusCases->value,
            'qrcode_token' => fake()->uuid(),
        ];
    }
}

