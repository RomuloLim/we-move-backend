<?php

namespace Modules\Operation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Operation\DTOs\InstitutionDto;

class InstitutionFormRequest extends FormRequest
{
    public function rules(): array
    {
        $institutionId = $this->route('institution');

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('institutions', 'name')->ignore($institutionId),
            ],
            'acronym' => ['nullable', 'string', 'max:10'],
            'street' => ['nullable', 'string', 'max:255'],
            'number' => ['nullable', 'string', 'max:20'],
            'complement' => ['nullable', 'string', 'max:100'],
            'neighborhood' => ['nullable', 'string', 'max:100'],
            'city' => ['required', 'string', 'max:100'],
            'state' => ['required', 'string', 'max:50'],
            'zip_code' => ['nullable', 'string', 'max:10'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome é obrigatório.',
            'name.unique' => 'Esta instituição já está cadastrada.',
            'city.required' => 'A cidade é obrigatória.',
            'state.required' => 'O estado é obrigatório.',
        ];
    }

    public function toDto(): InstitutionDto
    {
        $validated = $this->validated();

        return new InstitutionDto(
            name: $validated['name'],
            acronym: $validated['acronym'] ?? null,
            street: $validated['street'] ?? null,
            number: $validated['number'] ?? null,
            complement: $validated['complement'] ?? null,
            neighborhood: $validated['neighborhood'] ?? null,
            city: $validated['city'],
            state: $validated['state'],
            zip_code: $validated['zip_code'] ?? null,
        );
    }
}
