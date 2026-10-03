<?php

namespace App\Policies;

use App\Models\Building;
use App\Models\Charge;
use App\Models\User;

class ChargePolicy
{
    public function before(User $user): ?bool
    {
        return $user->role === 'admin' ? true : null;
    }

    public function view(User $user, Charge $charge): bool
    {
        return ($user->role === 'manager' && $charge->building->manager_id === $user->id)
            || ($user->role === 'resident' && $charge->apartment->resident_id === $user->id);
    }

    public function create(User $user, Building $building): bool
    {
        return $user->role === 'manager' && $building->manager_id === $user->id;
    }

    public function update(User $user, Charge $charge): bool
    {
        return $user->role === 'manager' && $charge->building->manager_id === $user->id;
    }

    public function submitReceipt(User $user, Charge $charge): bool
    {
        return $user->role === 'resident' && $charge->apartment->resident_id === $user->id && $charge->status !== 'paid';
    }
}
