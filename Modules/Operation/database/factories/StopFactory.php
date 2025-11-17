<?php

namespace Modules\Operation\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Operation\Models\Stop;

class StopFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = Stop::class;
    private static $order = 1;
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'route_id' => RouteFactory::new(),
            'stop_name' => $this->faker->word,
            'latitude' => $this->faker->latitude,
            'longitude' => $this->faker->longitude,
            'scheduled_time' => $this->faker->numberBetween(10, 120),
            'order' => self::$order++,
        ];
    }
}

