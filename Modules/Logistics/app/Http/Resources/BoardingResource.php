<?php

namespace Modules\Logistics\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BoardingResource extends JsonResource
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
            'trip_id' => $this->trip_id,
            'student_id' => $this->student_id,
            'boarding_timestamp' => $this->boarding_timestamp?->format('Y-m-d H:i:s'),
            'landed_at' => $this->landed_at?->format('Y-m-d H:i:s'),
            'is_boarded' => $this->landed_at === null,
            'stop_id' => $this->stop_id,
            'trip' => new TripResource($this->whenLoaded('trip')),
            'student' => $this->whenLoaded('student'),
            'stop' => new StopResource($this->whenLoaded('stop')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
