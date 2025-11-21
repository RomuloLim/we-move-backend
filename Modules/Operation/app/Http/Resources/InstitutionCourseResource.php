<?php

namespace Modules\Operation\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class InstitutionCourseResource extends JsonResource
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
            'institution_id' => $this->institution_id,
            'course_id' => $this->course_id,
            'institution' => new InstitutionResource($this->whenLoaded('institution')),
            'course' => new CourseResource($this->whenLoaded('course')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
