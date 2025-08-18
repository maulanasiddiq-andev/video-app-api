<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VideoResource extends JsonResource
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
            'title' => $this->title,
            'description' => $this->description,
            'image' => $this->image,
            'video' => $this->video,
            'duration' => $this->duration,
            'recordStatus' => $this->record_status,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
            'commentsCount' => $this->comments_count,
            'historiesCount' => $this->histories_count,
            'user' => new UserResource($this->whenLoaded('user')),
            'comment' => new CommentResource($this->whenLoaded('comment')),
            'history' => new HistoryResource($this->whenLoaded('history'))
        ];
    }
}
