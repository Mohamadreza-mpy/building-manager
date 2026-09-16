<?php

namespace App\Policies;

use App\Models\Building;
use App\Models\User;

class BuildingPolicy
{
    public function before(User $user): ?bool
    {
        return $user->role === 'admin' ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return $user->role === 'manager';
    }

    public function view(User $user, Building $building): bool
    {
        return $user->role === 'manager' && $building->manager_id === $user->id;
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'manager'], true);
    }

    public function update(User $user, Building $building): bool
    {
        return $user->role === 'manager' && $building->manager_id === $user->id;
    }

    public function delete(User $user, Building $building): bool
    {
        return $this->update($user, $building);
    }
}
