<?php

namespace Modules\Operation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Operation\Models\Stop;

class UpdateStopsOrderRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'stops' => [
                'required',
                'array',
                'min:1',
            ],
            'stops.*.stop_id' => [
                'required',
                'integer',
                'exists:stops,id',
                'distinct',
            ],
            'stops.*.order' => [
                'required',
                'integer',
                'min:1',
                'distinct',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'stops.required' => 'O campo paradas é obrigatório.',
            'stops.array' => 'O campo paradas deve ser um array.',
            'stops.min' => 'É necessário informar ao menos uma parada.',
            'stops.*.stop_id.required' => 'O ID da parada é obrigatório.',
            'stops.*.stop_id.exists' => 'Uma ou mais paradas não existem.',
            'stops.*.stop_id.distinct' => 'Não é permitido enviar paradas duplicadas.',
            'stops.*.order.required' => 'A ordem é obrigatória.',
            'stops.*.order.min' => 'A ordem deve ser no mínimo 1.',
            'stops.*.order.distinct' => 'Não é permitido ter paradas com a mesma ordem.',
        ];
    }

    /**
     * Validate that all stops belong to the same route
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $stops = $this->input('stops', []);

            if (empty($stops)) {
                return;
            }

            $stopIds = array_column($stops, 'stop_id');
            $routes = Stop::whereIn('id', $stopIds)
                ->pluck('route_id')
                ->unique();

            if ($routes->count() > 1) {
                $validator->errors()->add(
                    'stops',
                    'Todas as paradas devem pertencer à mesma rota.'
                );
            }
        });
    }
}
