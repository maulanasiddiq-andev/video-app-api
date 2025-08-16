<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HistoryResource extends JsonResource
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
            'duration' => $this->duration,
            'position' => $this->position,
            'recordStatus' => $this->record_status,
            'createdAt' => $this->created_at,
            'video' => new VideoResource($this->video),
        ];
    }
}
