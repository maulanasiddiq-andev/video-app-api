<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email,
            'emailVerifiedAt' => $this->email_verified_at,
            'recordStatus' => $this->record_status,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
            'profileImage' => $this->profile_image,
            'videosCount' => $this->videos_count
        ];
    }
}
