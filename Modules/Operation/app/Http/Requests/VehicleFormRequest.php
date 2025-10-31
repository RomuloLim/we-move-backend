<?php

namespace Modules\Operation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VehicleFormRequest extends FormRequest
{
    public function rules(): array
    {
        $vehicleId = $this->route('vehicle');

        return [
            'license_plate' => [
                'required',
                'string',
                'max:20',
                Rule::unique('vehicles', 'license_plate')->ignore($vehicleId),
            ],
            'model' => ['required', 'string', 'max:100'],
            'capacity' => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'license_plate.required' => 'A placa é obrigatória.',
            'license_plate.unique' => 'Esta placa já está cadastrada.',
            'model.required' => 'O modelo é obrigatório.',
            'capacity.required' => 'A capacidade é obrigatória.',
            'capacity.integer' => 'A capacidade deve ser um número inteiro.',
            'capacity.min' => 'A capacidade deve ser maior que zero.',
        ];
    }
}
