<?php

namespace App\Models;

final readonly class Owner
{
    public function __construct(
        public int $id,
        public string $name,
        public string $mobile,
        public ?string $email,
        public int $ownedApartmentsCount,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: (int) $data['id'],
            name: (string) $data['name'],
            mobile: (string) $data['mobile'],
            email: isset($data['email']) ? (string) $data['email'] : null,
            ownedApartmentsCount: (int) ($data['owned_apartments_count'] ?? 0),
        );
    }

    public function toArray(): array
    {
        return ['id' => $this->id, 'name' => $this->name, 'mobile' => $this->mobile, 'email' => $this->email, 'owned_apartments_count' => $this->ownedApartmentsCount];
    }
}
