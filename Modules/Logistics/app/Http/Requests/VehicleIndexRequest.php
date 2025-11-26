<?php

namespace Modules\Logistics\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VehicleIndexRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'search' => 'sometimes|string|max:255',
        ];
    }
}
