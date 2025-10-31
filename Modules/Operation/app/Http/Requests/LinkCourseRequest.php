<?php

namespace Modules\Operation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LinkCourseRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'courses_ids' => ['required', 'array', 'exists:courses,id'],
            'courses_ids.*' => ['required', 'integer'],
        ];
    }

    public function messages(): array
    {
        return [
            'courses_ids.required' => 'O campo cursos é obrigatório.',
            'courses_ids.array' => 'O campo cursos deve ser um array.',
            'courses_ids.exists' => 'Um ou mais cursos não existem.',
            'courses_ids.*.integer' => 'O ID do curso deve ser um número inteiro.',
            'courses_ids.*.exists' => 'O curso não existe.',
        ];
    }
}
