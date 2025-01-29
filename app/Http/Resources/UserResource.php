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
            'username' => $this->username,
            // Don't include the email
            // 'email' => $this->email, // This is now hidden
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}