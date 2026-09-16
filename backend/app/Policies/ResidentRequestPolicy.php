<?php

namespace App\Policies;

use App\Models\ResidentRequest;
use App\Models\User;

class ResidentRequestPolicy
{
    public function before(User $user): ?bool
    {
        return $user->role === 'admin' ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['manager', 'resident'], true);
    }

    public function create(User $user): bool
    {
        return $user->role === 'resident';
    }

    public function update(User $user, ResidentRequest $request): bool
    {
        return $user->role === 'manager' && $request->apartment->building->manager_id === $user->id;
    }
}
