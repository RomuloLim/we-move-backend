<?php

namespace Modules\Operation\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Operation\Enums\DocumentType;
use Modules\Operation\Models\Student;

class DocumentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = \Modules\Operation\Models\Document::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'student_id' => Student::factory(),
            'type' => $this->faker->randomElement(DocumentType::cases()),
            'file_url' => 'documents/' . $this->faker->unique()->bothify('??????????') . '.pdf',
            'uploaded_at' => $this->faker->dateTimeBetween('-1 years', 'now'),
        ];
    }
}
