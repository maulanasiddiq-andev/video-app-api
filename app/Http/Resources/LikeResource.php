<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LikeResource extends JsonResource
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
            'userId' => $this->user_id,
            'videoId' => $this->video_id,
            'user' => new UserResource($this->whenLoaded('user')),
            'video' => new VideoResource($this->whenLoaded('video'))
        ];
    }
}
