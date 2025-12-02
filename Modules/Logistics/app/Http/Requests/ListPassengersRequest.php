<?php

namespace Modules\Logistics\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ListPassengersRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'only_boarded' => [
                'sometimes',
                'nullable',
                'boolean',
            ],
        ];
    }

    public function messages(): array
    {
        return [];
    }

    /**
     * Prepara os dados para validação.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('only_boarded')) {
            $value = $this->boolean('only_boarded');

            $this->merge(['only_boarded' => $value]);
        } else {
            $this->merge(['only_boarded' => null]);
        }
    }
}
