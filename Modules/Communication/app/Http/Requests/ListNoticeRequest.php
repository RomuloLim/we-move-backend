<?php

namespace Modules\Communication\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ListNoticeRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'route_ids' => [
                'nullable',
                'array',
            ],
            'route_ids.*' => [
                'integer',
                'exists:routes,id',
            ],
            'per_page' => [
                'nullable',
                'integer',
                'min:1',
                'max:100',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'route_ids.array' => 'Os IDs das rotas devem ser enviados em um array.',
            'route_ids.*.integer' => 'Os IDs das rotas devem ser números inteiros.',
            'route_ids.*.exists' => 'Uma ou mais rotas selecionadas não existem.',
            'per_page.integer' => 'O número de itens por página deve ser um número inteiro.',
            'per_page.min' => 'O número mínimo de itens por página é 1.',
            'per_page.max' => 'O número máximo de itens por página é 100.',
        ];
    }
}
