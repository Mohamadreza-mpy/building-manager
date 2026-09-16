<?php

namespace App\Services;

use App\Models\Building;
use App\Models\User;

class BuildingService
{
    public function create(array $data, User $user): Building
    {
        return Building::create([
            'manager_id' => $user->id,
            'name' => $data['name'],
            'address' => $data['address'] ?? null,
            'total_units' => $data['total_units'] ?? 0,
        ]);
    }
}
