<?php

namespace Modules\Operation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\{Enum};
use Modules\Operation\DTOs\RequisitionListParamsDto;
use Modules\Operation\Enums\{AtuationForm, RequisitionStatus};

class RequisitionListRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'protocol' => 'sometimes|nullable|string|max:255',
            'status' => new Enum(RequisitionStatus::class),
            'institution_course_id' => 'sometimes|nullable|integer',
            'atuation_form' => new Enum(AtuationForm::class),
            'deny_reason' => 'sometimes|nullable|string|max:1000',
        ];
    }

    public function toDto(): RequisitionListParamsDto
    {
        $validated = $this->validated();

        $status = null;

        if ($this->has('status')) {
            $status = RequisitionStatus::tryFrom($validated['status']);
        }

        $actuationForm = null;

        if ($this->has('atuation_form')) {
            $actuationForm = AtuationForm::tryFrom($validated['atuation_form']);
        }

        return new RequisitionListParamsDto(
            protocol: $this->input('protocol'),
            status: $status,
            institution_course_id: $validated['institution_course_id'] ?? null,
            atuation_form: $actuationForm,
            deny_reason: $validated['deny_reason'] ?? null,
        );
    }
}
