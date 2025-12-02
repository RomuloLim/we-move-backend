<?php

namespace Modules\Logistics\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Logistics\Enums\VehicleAvailabilityFilter;

class VehicleIndexRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'search' => 'sometimes|string|max:255',
            'availability' => ['sometimes', 'string', Rule::enum(VehicleAvailabilityFilter::class)],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'availability.in' => 'O filtro de disponibilidade deve ser: all, available ou in_use.',
        ];
    }
}
