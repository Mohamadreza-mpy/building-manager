<?php

namespace App\Models;

final readonly class Resident
{
    public function __construct(
        public int $id,
        public string $name,
        public string $mobile,
        public ?string $email,
        public int $apartmentsCount,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: (int) $data['id'],
            name: (string) $data['name'],
            mobile: (string) $data['mobile'],
            email: isset($data['email']) ? (string) $data['email'] : null,
            apartmentsCount: (int) ($data['apartments_count'] ?? 0),
        );
    }

    public function toArray(): array
    {
        return ['id' => $this->id, 'name' => $this->name, 'mobile' => $this->mobile, 'email' => $this->email, 'apartments_count' => $this->apartmentsCount];
    }
}
