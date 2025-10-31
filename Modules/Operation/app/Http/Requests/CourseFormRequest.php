<?php

namespace Modules\Operation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Operation\DTOs\CourseDto;

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
        );
    }
}
