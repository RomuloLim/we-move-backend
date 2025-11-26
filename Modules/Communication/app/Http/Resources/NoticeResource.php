<?php

namespace Modules\Communication\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NoticeResource extends JsonResource
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
            'author_user_id' => $this->author_user_id,
            'title' => $this->title,
            'content' => $this->content,
            'type' => $this->type->value,
            'type_label' => $this->type->label(),
            'route_id' => $this->route_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
