<?php

namespace Modules\Logistics\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Logistics\DTOs\BoardingDto;

class BoardRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'trip_id' => [
                'required',
                'integer',
                'exists:trips,id',
            ],
            'student_id' => [
                'required',
                'integer',
                'exists:students,id',
            ],
            'stop_id' => [
                'required',
                'integer',
                'exists:stops,id',
            ],
            'qrcode_token' => [
                'required',
                'string',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'trip_id.required' => 'A viagem é obrigatória.',
            'trip_id.exists' => 'A viagem selecionada não existe.',
            'student_id.required' => 'O estudante é obrigatório.',
            'student_id.exists' => 'O estudante selecionado não existe.',
            'stop_id.required' => 'O ponto de parada é obrigatório.',
            'stop_id.exists' => 'O ponto de parada selecionado não existe.',
            'qrcode_token.required' => 'O token do QR Code é obrigatório.',
            'qrcode_token.string' => 'O token do QR Code deve ser uma string.',
        ];
    }

    public function toDto(): BoardingDto
    {
        $validated = $this->validated();

        return new BoardingDto(
            tripId: $validated['trip_id'],
            studentId: $validated['student_id'],
            stopId: $validated['stop_id'],
            qrcodeToken: $validated['qrcode_token'],
            driverId: auth()->id(),
        );
    }
}
