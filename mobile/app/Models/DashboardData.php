<?php

namespace App\Models;

final readonly class DashboardData
{
    public function __construct(public string $role, public array $data) {}

    public static function fromArray(array $data): self
    {
        return new self((string) ($data['role'] ?? 'resident'), $data);
    }

    public function isResident(): bool
    {
        return $this->role === 'resident';
    }
}
