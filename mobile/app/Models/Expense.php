<?php

namespace App\Models;

final readonly class Expense
{
    public function __construct(
        public int $id,
        public int $buildingId,
        public string $title,
        public string $amount,
        public string $category,
        public ?string $description,
        public ?string $imageUrl,
        public int $createdBy,
        public ?array $creator,
        public ?string $createdAt,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: (int) $data['id'],
            buildingId: (int) $data['building_id'],
            title: (string) $data['title'],
            amount: (string) $data['amount'],
            category: (string) $data['category'],
            description: isset($data['description']) ? (string) $data['description'] : null,
            imageUrl: isset($data['image_url']) ? (string) $data['image_url'] : null,
            createdBy: (int) $data['created_by'],
            creator: is_array($data['creator'] ?? null) ? $data['creator'] : null,
            createdAt: isset($data['created_at']) ? (string) $data['created_at'] : null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'building_id' => $this->buildingId,
            'title' => $this->title,
            'amount' => $this->amount,
            'category' => $this->category,
            'description' => $this->description,
            'image_url' => $this->imageUrl,
            'created_by' => $this->createdBy,
            'creator' => $this->creator,
            'created_at' => $this->createdAt,
        ];
    }
}
