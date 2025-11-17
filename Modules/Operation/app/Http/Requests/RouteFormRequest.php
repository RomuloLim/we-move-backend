<?php

namespace Modules\Operation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Operation\DTOs\RouteDto;

class RouteFormRequest extends FormRequest
{
    public function rules(): array
    {
        $routeId = $this->route('route');

        return [
            'route_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('routes', 'route_name')->ignore($routeId),
            ],
            'description' => [
                'nullable',
                'string',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'route_name.required' => 'O nome da rota é obrigatório.',
            'route_name.unique' => 'Esta rota já está cadastrada.',
            'route_name.max' => 'O nome da rota não pode ter mais de 255 caracteres.',
        ];
    }

    public function toDto(): RouteDto
    {
        $validated = $this->validated();

        return new RouteDto(
            routeName: $validated['route_name'],
            description: $validated['description'] ?? null,
        );
    }
}
