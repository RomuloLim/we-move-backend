<?php

namespace Modules\Logistics\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Logistics\DTOs\TripDto;

class StartTripRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'route_id' => [
                'required',
                'integer',
                'exists:routes,id',
            ],
            'vehicle_id' => [
                'required',
                'integer',
                'exists:vehicles,id',
            ],
            'trip_date' => [
                'required',
                'date',
                'date_format:Y-m-d',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'route_id.required' => 'A rota é obrigatória.',
            'route_id.exists' => 'A rota selecionada não existe.',
            'vehicle_id.required' => 'O veículo é obrigatório.',
            'vehicle_id.exists' => 'O veículo selecionado não existe.',
            'trip_date.required' => 'A data da viagem é obrigatória.',
            'trip_date.date' => 'A data da viagem deve ser uma data válida.',
            'trip_date.date_format' => 'A data da viagem deve estar no formato Y-m-d.',
        ];
    }

    public function toDto(): TripDto
    {
        $validated = $this->validated();

        return new TripDto(
            routeId: $validated['route_id'],
            driverId: auth()->id(),
            vehicleId: $validated['vehicle_id'],
            tripDate: $validated['trip_date'],
        );
    }
}
