<?php

namespace Modules\Logistics\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Logistics\Models\Route;

class RouteFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = Route::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'route_name' => $this->faker->word,
            'description' => $this->faker->sentence,
        ];
    }
}
