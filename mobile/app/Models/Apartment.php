<?php

namespace App\Models;

final readonly class Apartment
{
    public function __construct(
        public int $id,
        public int $buildingId,
        public ?int $ownerId,
        public ?int $residentId,
        public string $number,
        public ?int $floor,
        public ?float $area,
        public ?array $resident,
        public ?array $owner,
        public ?array $building,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: (int) $data['id'],
            buildingId: (int) $data['building_id'],
            ownerId: isset($data['owner_id']) ? (int) $data['owner_id'] : null,
            residentId: isset($data['resident_id']) ? (int) $data['resident_id'] : null,
            number: (string) $data['number'],
            floor: isset($data['floor']) ? (int) $data['floor'] : null,
            area: isset($data['area']) ? (float) $data['area'] : null,
            resident: is_array($data['resident'] ?? null) ? $data['resident'] : null,
            owner: is_array($data['owner'] ?? null) ? $data['owner'] : null,
            building: is_array($data['building'] ?? null) ? $data['building'] : null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'building_id' => $this->buildingId,
            'owner_id' => $this->ownerId,
            'resident_id' => $this->residentId,
            'number' => $this->number,
            'floor' => $this->floor,
            'area' => $this->area,
            'resident' => $this->resident,
            'owner' => $this->owner,
            'building' => $this->building,
        ];
    }
}
