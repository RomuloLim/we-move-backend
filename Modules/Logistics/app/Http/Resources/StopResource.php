<?php

namespace Modules\Logistics\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StopResource extends JsonResource
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
            'route_id' => $this->route_id,
            'stop_name' => $this->stop_name,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'scheduled_time' => $this->scheduled_time,
            'order' => $this->order,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
