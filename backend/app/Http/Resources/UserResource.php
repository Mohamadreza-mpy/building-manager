<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'mobile' => $this->mobile,
            'email' => $this->email,
            'role' => $this->role,
            'owned_apartments_count' => $this->whenCounted('ownedApartments'),
            'apartments_count' => $this->whenCounted('apartments'),
        ];
    }
}
