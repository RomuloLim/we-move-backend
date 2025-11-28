<?php

namespace Modules\Logistics\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Logistics\Models\{Boarding, Stop, Trip};
use Modules\Operation\Models\Student;

class BoardingFactory extends Factory
{
    protected $model = Boarding::class;

    public function definition(): array
    {
        return [
            'trip_id' => Trip::factory(),
            'student_id' => Student::factory(),
            'boarding_timestamp' => $this->faker->dateTimeBetween('-2 hours', 'now'),
            'landed_at' => null,
            'stop_id' => Stop::factory(),
        ];
    }

    public function landed(): static
    {
        return $this->state(fn (array $attributes) => [
            'landed_at' => $this->faker->dateTimeBetween($attributes['boarding_timestamp'], 'now'),
        ]);
    }
}
