<?php

namespace App\Services;

use App\Contracts\TokenStorage;
use App\Exceptions\ApiException;
use App\Models\User;
use App\State\AuthState;

class AuthService
{
    public function __construct(
        private readonly ApiClient $api,
        private readonly TokenStorage $tokenStorage,
        private readonly AuthState $state,
    ) {}

    public function login(string $mobile, string $password): User
    {
        $response = $this->api->post('/auth/login', [
            'mobile' => $mobile,
            'password' => $password,
        ]);

        if (! is_array($response->data) || ! isset($response->data['token'], $response->data['user'])) {
            throw new ApiException('پاسخ دریافتی از سرور معتبر نیست.');
        }

        $user = User::fromArray($response->data['user']);
        $this->tokenStorage->store((string) $response->data['token']);
        $this->state->setUser($user);

        return $user;
    }

    public function currentUser(bool $refresh = false): ?User
    {
        if (! $this->tokenStorage->get()) {
            return null;
        }

        $cachedUser = $this->state->user();

        if ($cachedUser && ! $refresh) {
            return $cachedUser;
        }

        $response = $this->api->get('/auth/me');

        if (! is_array($response->data)) {
            throw new ApiException('اطلاعات کاربر از سرور دریافت نشد.');
        }

        $user = User::fromArray($response->data);
        $this->state->setUser($user);

        return $user;
    }

    public function logout(): void
    {
        try {
            if ($this->tokenStorage->get()) {
                $this->api->post('/auth/logout');
            }
        } finally {
            $this->tokenStorage->forget();
            $this->state->clear();
        }
    }

    public function hasToken(): bool
    {
        return filled($this->tokenStorage->get());
    }
}
