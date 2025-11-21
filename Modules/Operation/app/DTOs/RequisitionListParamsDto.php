<?php

namespace Modules\Operation\DTOs;

use Modules\Operation\Enums\{AtuationForm, RequisitionStatus};

class RequisitionListParamsDto
{
    public function __construct(
        public ?string $protocol = null,
        public ?RequisitionStatus $status = RequisitionStatus::Pending,
        public ?int $institution_course_id = null,
        public ?AtuationForm $atuation_form = null,
        public ?string $deny_reason = null,
    ) {}

    public function toArray(): array
    {
        return [
            'protocol' => $this->protocol,
            'status' => $this->status?->value,
            'institution_course_id' => $this->institution_course_id,
            'atuation_form' => $this->atuation_form?->value,
            'deny_reason' => $this->deny_reason,
        ];
    }
}
