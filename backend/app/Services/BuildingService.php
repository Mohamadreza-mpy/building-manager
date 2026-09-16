<?php

namespace App\Services;

use App\Models\Building;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class BuildingService
{
    public function listFor(User $user): LengthAwarePaginator
    {
        return Building::query()
            ->with('manager')
            ->withCount('apartments')
            ->when($user->role !== 'admin', fn ($query) => $query->where('manager_id', $user->id))
            ->latest()
            ->paginate(15);
    }

    public function create(array $data, User $user): Building
    {
        return Building::create([
            'manager_id' => $user->id,
            'name' => $data['name'],
            'address' => $data['address'] ?? null,
            'total_units' => $data['total_units'] ?? null,
        ])->load('manager')->loadCount('apartments');
    }

    public function find(Building $building): Building
    {
        return $building->load('manager')->loadCount('apartments');
    }

    public function update(Building $building, array $data): Building
    {
        $building->update($data);

        return $building->refresh()->load('manager')->loadCount('apartments');
    }

    public function delete(Building $building): void
    {
        $building->delete();
    }
}
