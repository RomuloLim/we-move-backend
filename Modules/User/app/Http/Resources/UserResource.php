<?php

namespace Modules\User\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'cpf' => $this->cpf,
            'rg' => $this->rg,
            'gender' => $this->gender,
            'gender_label' => $this->gender_text,
            'phone_contact' => $this->phone_contact,
            'user_type' => $this->user_type,
            'user_type_label' => $this->user_type->label(),
            'student_profile' => $this->whenLoaded('studentProfile'),
            'email_verified_at' => $this->email_verified_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
