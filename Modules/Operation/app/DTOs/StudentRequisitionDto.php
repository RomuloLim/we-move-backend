<?php

namespace Modules\Operation\DTOs;

use DateTimeInterface;
use Modules\Operation\Enums\{AtuationForm, RequisitionStatus};

class StudentRequisitionDto
{
    public function __construct(
        public int $student_id,
        public string $semester,
        public ?string $protocol = null,
        public RequisitionStatus $status = RequisitionStatus::Pending,
        public ?string $street_name = null,
        public ?string $house_number = null,
        public ?string $neighborhood = null,
        public ?string $city = null,
        public ?string $phone_contact = null,
        public ?DateTimeInterface $birth_date = null,
        public ?string $institution_email = null,
        public ?string $institution_registration = null,
        public ?int $institution_id = null,
        public ?int $course_id = null,
        public ?AtuationForm $atuation_form = null,
        public ?string $deny_reason = null,
    ) {}

    public function toArray(): array
    {
        return array_filter([
            'student_id' => $this->student_id,
            'semester' => $this->semester,
            'protocol' => $this->protocol,
            'status' => $this->status,
            'street_name' => $this->street_name,
            'house_number' => $this->house_number,
            'neighborhood' => $this->neighborhood,
            'city' => $this->city,
            'phone_contact' => $this->phone_contact,
            'birth_date' => $this->birth_date,
            'institution_email' => $this->institution_email,
            'institution_registration' => $this->institution_registration,
            'institution_id' => $this->institution_id,
            'course_id' => $this->course_id,
            'atuation_form' => $this->atuation_form,
            'deny_reason' => $this->deny_reason,
        ], fn ($value): bool => $value !== null);
    }
}
