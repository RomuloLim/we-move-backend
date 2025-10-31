<?php

namespace Modules\Operation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LinkCourseRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'course_id' => ['required', 'integer', 'exists:courses,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'course_id.required' => 'O ID do curso é obrigatório.',
            'course_id.integer' => 'O ID do curso deve ser um número inteiro.',
            'course_id.exists' => 'O curso não existe.',
        ];
    }
}
