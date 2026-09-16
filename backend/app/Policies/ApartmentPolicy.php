<?php

namespace App\Policies;

use App\Models\Apartment;
use App\Models\Building;
use App\Models\User;

class ApartmentPolicy
{
    public function before(User $user): ?bool
    {
        return $user->role === 'admin' ? true : null;
    }

    public function view(User $user, Apartment $apartment): bool
    {
        return $apartment->resident_id === $user->id || ($user->role === 'manager' && $apartment->building->manager_id === $user->id);
    }

    public function create(User $user, Building $building): bool
    {
        return $user->role === 'manager' && $building->manager_id === $user->id;
    }

    public function update(User $user, Apartment $apartment): bool
    {
        return $user->role === 'manager' && $apartment->building->manager_id === $user->id;
    }

    public function delete(User $user, Apartment $apartment): bool
    {
        return $this->update($user, $apartment);
    }
}
