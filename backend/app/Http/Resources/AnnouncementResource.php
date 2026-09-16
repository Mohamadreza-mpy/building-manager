<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AnnouncementResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return ['id' => $this->id, 'building_id' => $this->building_id, 'title' => $this->title, 'body' => $this->body, 'created_by' => $this->created_by, 'creator' => new UserResource($this->whenLoaded('creator')), 'created_at' => $this->created_at?->toISOString()];
    }
}
