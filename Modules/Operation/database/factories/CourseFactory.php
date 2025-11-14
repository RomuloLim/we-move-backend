<?php

namespace Modules\Operation\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Operation\Enums\CourseType;
use Modules\Operation\Models\Course;

class CourseFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = Course::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $courseTypes = CourseType::cases();

        return [
            'name' => $this->faker->unique()->words(3, true),
            'course_type' => $this->faker->randomElement($courseTypes)->value,
            'description' => $this->faker->optional()->paragraph(),
        ];
    }
}
