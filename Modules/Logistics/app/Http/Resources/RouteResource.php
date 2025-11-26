<?php

namespace Modules\Logistics\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RouteResource extends JsonResource
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
            'route_name' => $this->route_name,
            'description' => $this->description,
            'stops' => StopResource::collection($this->whenLoaded('stops')),
            'stops_amount' => $this->when(isset($this->stops_amount), $this->stops_amount),
            'first_stop' => new StopResource($this->whenLoaded('firstStop')),
            'last_stop' => new StopResource($this->whenLoaded('lastStop')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
