<?php

namespace App\Services;

use App\Models\Apartment;
use App\Models\Building;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ApartmentService
{
    public function list(Building $building): LengthAwarePaginator
    {
        return $building->apartments()->with(['building', 'owner', 'resident'])->orderBy('floor')->orderBy('number')->paginate(20);
    }

    public function create(Building $building, array $data): Apartment
    {
        return $building->apartments()->create($data)->load(['building', 'owner', 'resident']);
    }

    public function find(Apartment $apartment): Apartment
    {
        return $apartment->load(['building', 'owner', 'resident']);
    }

    public function update(Apartment $apartment, array $data): Apartment
    {
        $apartment->update($data);

        return $apartment->refresh()->load(['building', 'owner', 'resident']);
    }

    public function delete(Apartment $apartment): void
    {
        $apartment->delete();
    }
}
