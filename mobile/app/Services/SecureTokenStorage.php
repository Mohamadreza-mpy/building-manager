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
        if ($this->isNativeRuntime()) {
            return SecureStorage::get(self::KEY);
        }

        return session()->get(self::KEY);
    }

    public function store(string $token): void
    {
        if ($this->isNativeRuntime()) {
            if (! SecureStorage::set(self::KEY, $token)) {
                throw new RuntimeException('ذخیره امن نشست کاربری انجام نشد.');
            }

            return;
        }

        session()->put(self::KEY, $token);
    }

    public function forget(): void
    {
        if ($this->isNativeRuntime()) {
            SecureStorage::delete(self::KEY);

            return;
        }

        session()->forget(self::KEY);
    }

    private function isNativeRuntime(): bool
    {
        return (bool) config('nativephp-internal.running', false)
            || getenv('JUMP_BRIDGE_PORT') !== false;
    }
}
