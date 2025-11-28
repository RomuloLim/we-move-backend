<?php

namespace Modules\Logistics\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Logistics\Enums\TripStatus;
use Modules\Logistics\Models\{Route, Trip, Vehicle};
use Modules\User\Models\User;

class TripFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = Trip::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'route_id' => Route::factory(),
            'driver_id' => User::factory(),
            'vehicle_id' => Vehicle::factory(),
            'trip_date' => $this->faker->dateTimeBetween('-30 days', '+30 days')->format('Y-m-d'),
            'status' => $this->faker->randomElement(TripStatus::cases()),
        ];
    }

    public function scheduled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => TripStatus::Scheduled,
        ]);
    }

    public function inProgress(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => TripStatus::InProgress,
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => TripStatus::Completed,
        ]);
    }

    public function canceled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => TripStatus::Canceled,
        ]);
    }
}
