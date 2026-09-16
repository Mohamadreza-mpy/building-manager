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
        if ($this->isPackagedNativeRuntime()) {
            return SecureStorage::get(self::KEY);
        }

        if ($this->isJumpRuntime()) {
            return SecureStorage::get(self::KEY) ?? session()->get(self::KEY);
        }

        return session()->get(self::KEY);
    }

    public function store(string $token): void
    {
        if ($this->isPackagedNativeRuntime()) {
            if (! SecureStorage::set(self::KEY, $token)) {
                throw new RuntimeException('ذخیره امن نشست کاربری انجام نشد.');
            }

            return;
        }

        if ($this->isJumpRuntime()) {
            if (! SecureStorage::set(self::KEY, $token)) {
                session()->put(self::KEY, $token);
            }

            return;
        }

        session()->put(self::KEY, $token);
    }

    public function forget(): void
    {
        if ($this->isPackagedNativeRuntime()) {
            SecureStorage::delete(self::KEY);

            return;
        }

        if ($this->isJumpRuntime()) {
            SecureStorage::delete(self::KEY);
        }

        session()->forget(self::KEY);
    }

    private function isPackagedNativeRuntime(): bool
    {
        return (bool) config('nativephp-internal.running', false);
    }

    private function isJumpRuntime(): bool
    {
        return getenv('JUMP_BRIDGE_PORT') !== false;
    }
}
