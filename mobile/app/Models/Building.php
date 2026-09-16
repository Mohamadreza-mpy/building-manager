<?php

namespace App\Models;

final readonly class Building
{
    public function __construct(
        public int $id,
        public string $name,
        public ?string $address,
        public ?int $totalUnits,
        public int $apartmentsCount,
        public ?array $manager,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: (int) $data['id'],
            name: (string) $data['name'],
            address: isset($data['address']) ? (string) $data['address'] : null,
            totalUnits: isset($data['total_units']) ? (int) $data['total_units'] : null,
            apartmentsCount: (int) ($data['apartments_count'] ?? 0),
            manager: is_array($data['manager'] ?? null) ? $data['manager'] : null,
        );
    }

    public function toArray(): array
    {
        return ['id' => $this->id, 'name' => $this->name, 'address' => $this->address, 'total_units' => $this->totalUnits, 'apartments_count' => $this->apartmentsCount, 'manager' => $this->manager];
    }
}
