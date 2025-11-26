<?php

namespace Modules\Communication\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Communication\Enums\NoticeType;
use Modules\Communication\Models\Notice;
use Modules\Logistics\Models\Route;
use Modules\User\Enums\UserType;
use Modules\User\Models\User;

class NoticeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = Notice::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'author_user_id' => User::factory()->create(['user_type' => UserType::Admin->value])->id,
            'title' => $this->faker->sentence(6),
            'content' => $this->faker->paragraphs(3, true),
            'type' => $this->faker->randomElement(NoticeType::cases()),
            'route_id' => null,
        ];
    }

    /**
     * Indicate that the notice is a general notice.
     */
    public function general(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => NoticeType::General,
            'route_id' => null,
        ]);
    }

    /**
     * Indicate that the notice is a route alert.
     */
    public function routeAlert(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => NoticeType::RouteAlert,
            'route_id' => Route::factory()->create()->id,
        ]);
    }
}
