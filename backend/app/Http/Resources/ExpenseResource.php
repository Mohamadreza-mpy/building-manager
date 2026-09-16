<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExpenseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return ['id' => $this->id, 'building_id' => $this->building_id, 'title' => $this->title, 'amount' => $this->amount, 'category' => $this->category, 'description' => $this->description, 'image_url' => $this->image ? asset('storage/'.$this->image) : null, 'created_by' => $this->created_by, 'creator' => new UserResource($this->whenLoaded('creator')), 'created_at' => $this->created_at?->toISOString()];
    }
}
