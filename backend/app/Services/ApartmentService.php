<?php

namespace App\Services;

use App\Models\Apartment;
use App\Models\Building;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ApartmentService
{
    public function list(Building $building): LengthAwarePaginator
    {
        return $building->apartments()->with(['building', 'resident'])->orderBy('floor')->orderBy('number')->paginate(20);
    }

    public function create(Building $building, array $data): Apartment
    {
        return $building->apartments()->create($data)->load(['building', 'resident']);
    }

    public function find(Apartment $apartment): Apartment
    {
        return $apartment->load(['building', 'resident']);
    }

    public function update(Apartment $apartment, array $data): Apartment
    {
        $apartment->update($data);

        return $apartment->refresh()->load(['building', 'resident']);
    }

    public function delete(Apartment $apartment): void
    {
        $apartment->delete();
    }
}
