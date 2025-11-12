<?php

namespace Modules\User\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use Modules\User\DTOs\SearchParamsDTO;
use Modules\User\Enums\UserType;

class ListUserRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'search' => ['sometimes', 'string', 'max:255'],
            'type' => ['sometimes', 'string', new Enum(UserType::class)]
        ];
    }

    public function toDto(): SearchParamsDTO
    {
        return new SearchParamsDTO(
            search: $this->input('search'),
            type: $this->enum('type', UserType::class),
            perPage: $this->input('per_page', 15),
        );
    }
}
