<?php

namespace Modules\Operation\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Operation\Models\Vehicle;

class VehicleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = Vehicle::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'license_plate' => $this->faker->unique()->bothify('??####'),
            'model' => $this->faker->word(),
            'capacity' => $this->faker->numberBetween(20, 50),
        ];
    }
}
