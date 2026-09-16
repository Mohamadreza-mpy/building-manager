<?php

namespace Tests\Support;

use App\Contracts\TokenStorage;

class InMemoryTokenStorage implements TokenStorage
{
    public ?string $token = null;

    public function get(): ?string
    {
        return $this->token;
    }

    public function store(string $token): void
    {
        $this->token = $token;
    }

    public function forget(): void
    {
        $this->token = null;
    }
}
