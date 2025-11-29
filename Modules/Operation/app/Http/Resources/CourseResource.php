<?php

namespace Modules\Operation\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
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
            'name' => $this->name,
            'course_type' => $this->course_type,
            'description' => $this->description,
            'is_linked' => $this->whenNotNull($this->is_linked),
            'institution_course_id' => $this->whenNotNull($this->pivot?->id),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
