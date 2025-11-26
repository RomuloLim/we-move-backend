<?php

namespace Modules\Operation\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Auth\Resources\UserResource;

class StudentRequisitionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'student_id' => $this->student_id,
            'protocol' => $this->protocol,
            'status' => $this->status,
            'semester' => $this->semester,
            'street_name' => $this->street_name,
            'house_number' => $this->house_number,
            'neighborhood' => $this->neighborhood,
            'city' => $this->city,
            'phone_contact' => $this->phone_contact,
            'birth_date' => $this->birth_date,
            'atuation_form' => $this->atuation_form,
            'deny_reason' => $this->deny_reason,
            'reproved_fields' => $this->reproved_fields,
            'institution_course' => new InstitutionCourseResource($this->whenLoaded('institutionCourse')),
            'student' => $this->whenLoaded('student'),
            'documents' => $this->whenLoaded('documents'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
