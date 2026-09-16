<?php

namespace App\State;

use App\Models\User;

class AuthState
{
    private const USER_KEY = 'building_manager_auth_user';

    public function user(): ?User
    {
        $data = session()->get(self::USER_KEY);

        return is_array($data) ? User::fromArray($data) : null;
    }

    public function setUser(User $user): void
    {
        session()->put(self::USER_KEY, $user->toArray());
    }

    public function clear(): void
    {
        session()->forget(self::USER_KEY);
    }
}
