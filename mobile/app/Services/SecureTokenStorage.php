<?php

namespace App\Services;

use App\Contracts\TokenStorage;
use Native\Mobile\Facades\SecureStorage;
use RuntimeException;

class SecureTokenStorage implements TokenStorage
{
    private const KEY = 'building_manager_auth_token';

    public function get(): ?string
    {
        if (function_exists('nativephp_call')) {
            return SecureStorage::get(self::KEY);
        }

        return session()->get(self::KEY);
    }

    public function store(string $token): void
    {
        if (function_exists('nativephp_call')) {
            if (! SecureStorage::set(self::KEY, $token)) {
                throw new RuntimeException('ذخیره امن نشست کاربری انجام نشد.');
            }

            return;
        }

        session()->put(self::KEY, $token);
    }

    public function forget(): void
    {
        if (function_exists('nativephp_call')) {
            SecureStorage::delete(self::KEY);

            return;
        }

        session()->forget(self::KEY);
    }
}
