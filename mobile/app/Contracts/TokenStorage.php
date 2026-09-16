<?php

namespace App\Contracts;

interface TokenStorage
{
    public function get(): ?string;

    public function store(string $token): void;

    public function forget(): void;
}
