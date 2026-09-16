<?php

namespace App\Policies;

use App\Models\Building;
use App\Models\User;

class ExpensePolicy
{
    public function before(User $user): ?bool
    {
        return $user->role === 'admin' ? true : null;
    }

    public function viewAny(User $user, Building $building): bool
    {
        return ($user->role === 'manager' && $building->manager_id === $user->id) || $building->apartments()->where('resident_id', $user->id)->exists();
    }

    public function create(User $user, Building $building): bool
    {
        return $user->role === 'manager' && $building->manager_id === $user->id;
    }
}
