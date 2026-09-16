<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return ['id' => $this->id, 'type' => $this->data['event'] ?? null, 'title' => $this->data['title'] ?? null, 'body' => $this->data['body'] ?? null, 'meta' => $this->data['meta'] ?? [], 'read_at' => $this->read_at?->toISOString(), 'created_at' => $this->created_at?->toISOString()];
    }
}
