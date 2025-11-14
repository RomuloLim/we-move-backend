<?php

namespace Modules\Operation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Modules\Operation\DTOs\CourseDto;
use Modules\Operation\Enums\CourseType;

class CourseFormRequest extends FormRequest
{
    public function rules(): array
    {
        $courseId = $this->route('course');

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('courses', 'name')->ignore($courseId),
            ],
            'course_type' => [
                'required',
                'string',
                new Enum(CourseType::class),
            ],
            'description' => [
                'nullable',
                'string',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome é obrigatório.',
            'name.unique' => 'Este curso já está cadastrado.',
        ];
    }

    public function toDto(): CourseDto
    {
        $validated = $this->validated();

        return new CourseDto(
            name: $validated['name'],
            courseType: CourseType::from($validated['course_type']),
            description: $validated['description'] ?? null,
        );
    }
}
