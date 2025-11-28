<?php

namespace Modules\Logistics\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UnboardRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'trip_id' => [
                'required',
                'integer',
                'exists:trips,id',
            ],
            'student_id' => [
                'required',
                'integer',
                'exists:students,id',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'trip_id.required' => 'A viagem é obrigatória.',
            'trip_id.exists' => 'A viagem selecionada não existe.',
            'student_id.required' => 'O estudante é obrigatório.',
            'student_id.exists' => 'O estudante selecionado não existe.',
        ];
    }
}
