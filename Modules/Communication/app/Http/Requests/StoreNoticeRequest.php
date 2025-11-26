<?php

namespace Modules\Communication\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use Modules\Communication\Enums\NoticeType;

class StoreNoticeRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => [
                'required',
                'string',
                'max:255',
            ],
            'content' => [
                'required',
                'string',
            ],
            'type' => [
                'required',
                'string',
                new Enum(NoticeType::class),
            ],
            'route_ids' => [
                'nullable',
                'array',
                'required_if:type,route_alert',
            ],
            'route_ids.*' => [
                'integer',
                'exists:routes,id',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'O título é obrigatório.',
            'title.max' => 'O título não pode ter mais de 255 caracteres.',
            'content.required' => 'O conteúdo é obrigatório.',
            'type.required' => 'O tipo é obrigatório.',
            'route_ids.required_if' => 'Ao menos uma rota deve ser selecionada para alertas de rota.',
            'route_ids.array' => 'As rotas devem ser enviadas em um array.',
            'route_ids.*.integer' => 'Os IDs das rotas devem ser números inteiros.',
            'route_ids.*.exists' => 'Uma ou mais rotas selecionadas não existem.',
        ];
    }
}
