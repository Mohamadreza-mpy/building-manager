<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApartmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'building_id' => $this->building_id,
            'owner_id' => $this->owner_id,
            'resident_id' => $this->resident_id,
            'number' => $this->number,
            'floor' => $this->floor,
            'area' => $this->area,
            'resident' => new UserResource($this->whenLoaded('resident')),
            'owner' => new UserResource($this->whenLoaded('owner')),
            'building' => new BuildingResource($this->whenLoaded('building')),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
