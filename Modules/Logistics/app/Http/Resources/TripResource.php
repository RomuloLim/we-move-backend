<?php

namespace Modules\Logistics\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\User\Http\Resources\UserResource;

class TripResource extends JsonResource
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
            'driver_id' => $this->driver_id,
            'vehicle_id' => $this->vehicle_id,
            'trip_date' => $this->trip_date?->format('Y-m-d'),
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'route' => new RouteResource($this->whenLoaded('route')),
            'driver' => new UserResource($this->whenLoaded('driver')),
            'vehicle' => new VehicleResource($this->whenLoaded('vehicle')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
