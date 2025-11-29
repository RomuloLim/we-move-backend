<?php

namespace Modules\Operation\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Logistics\Http\Resources\TripResource;
use Modules\User\Http\Resources\UserResource;

class StudentResource extends JsonResource
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
            'user_id' => $this->user_id,
            'institution_course_id' => $this->institution_course_id,
            'city_of_origin' => $this->city_of_origin,
            'status' => $this->status,
            'qrcode_token' => $this->qrcode_token,
            'user' => new UserResource($this->whenLoaded('user')),
            'latest_requisition' => $this->when(
                $this->relationLoaded('requisitions') && $this->requisitions->isNotEmpty(),
                fn () => new StudentRequisitionResource($this->requisitions->first())
            ),
            'available_trips' => TripResource::collection($this->when(
                isset($this->available_trips),
                $this->available_trips ?? []
            )),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
