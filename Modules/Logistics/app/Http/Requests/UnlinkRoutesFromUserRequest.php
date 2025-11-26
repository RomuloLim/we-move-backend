<?php

namespace Modules\Logistics\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Logistics\DTOs\UserRouteDto;

class UnlinkRoutesFromUserRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'user_id' => [
                'required',
                'integer',
                'exists:users,id',
            ],
            'route_ids' => [
                'required',
                'array',
                'min:1',
            ],
            'route_ids.*' => [
                'required',
                'integer',
                'exists:routes,id',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'O ID do usuário é obrigatório.',
            'user_id.exists' => 'O usuário informado não existe.',
            'route_ids.required' => 'É necessário informar ao menos uma rota.',
            'route_ids.array' => 'As rotas devem ser enviadas em formato de array.',
            'route_ids.min' => 'É necessário informar ao menos uma rota.',
            'route_ids.*.exists' => 'Uma ou mais rotas informadas não existem.',
        ];
    }

    public function toDto(): UserRouteDto
    {
        $validated = $this->validated();

        return new UserRouteDto(
            userId: $validated['user_id'],
            routeIds: $validated['route_ids'],
        );
    }
}
