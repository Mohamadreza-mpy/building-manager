<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RequestResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return ['id' => $this->id, 'apartment_id' => $this->apartment_id, 'title' => $this->title, 'description' => $this->description, 'status' => $this->status, 'response' => $this->response, 'apartment' => new ApartmentResource($this->whenLoaded('apartment')), 'created_at' => $this->created_at?->toISOString(), 'updated_at' => $this->updated_at?->toISOString()];
    }
}
