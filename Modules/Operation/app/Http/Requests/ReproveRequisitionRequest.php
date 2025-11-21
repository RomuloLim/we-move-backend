<?php

namespace Modules\Operation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use Modules\Operation\Enums\ReprovedFieldEnum;

class ReproveRequisitionRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'deny_reason' => ['required', 'string', 'max:1000'],
            'reproved_fields' => ['required', 'array', 'min:1'],
            'reproved_fields.*' => [new Enum(ReprovedFieldEnum::class)],
        ];
    }
}
