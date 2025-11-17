<?php

namespace Modules\Operation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Operation\DTOs\StopDto;

class StopFormRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'route_id' => [
                'required',
                'integer',
                'exists:routes,id',
            ],
            'stop_name' => [
                'required',
                'string',
                'max:255',
            ],
            'latitude' => [
                'nullable',
                'numeric',
                'between:-90,90',
            ],
            'longitude' => [
                'nullable',
                'numeric',
                'between:-180,180',
            ],
            'scheduled_time' => [
                'nullable',
                'date_format:H:i',
            ],
            // 'order' => [
            //     'nullable',
            //     'integer',
            //     'min:1',
            // ],
        ];
    }

    public function messages(): array
    {
        return [
            'route_id.required' => 'A rota é obrigatória.',
            'route_id.exists' => 'A rota informada não existe.',
            'stop_name.required' => 'O nome da parada é obrigatório.',
            'stop_name.max' => 'O nome da parada não pode ter mais de 255 caracteres.',
            'latitude.between' => 'A latitude deve estar entre -90 e 90.',
            'longitude.between' => 'A longitude deve estar entre -180 e 180.',
            'scheduled_time.date_format' => 'O horário programado deve estar no formato HH:MM.',
            'order.min' => 'A ordem deve ser no mínimo 1.',
        ];
    }

    public function toDto(): StopDto
    {
        $validated = $this->validated();

        return new StopDto(
            routeId: $validated['route_id'],
            stopName: $validated['stop_name'],
            latitude: $validated['latitude'] ?? null,
            longitude: $validated['longitude'] ?? null,
            scheduledTime: $validated['scheduled_time'] ?? null,
            order: $validated['order'] ?? null,
        );
    }
}
