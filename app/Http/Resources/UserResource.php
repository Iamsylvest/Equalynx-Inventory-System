<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email, // Include the email instead of username
            'role' => $this->role, // Add the role to the response
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}