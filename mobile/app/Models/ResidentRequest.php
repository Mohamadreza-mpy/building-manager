<?php

namespace App\Models;

final readonly class ResidentRequest
{
    public function __construct(
        public int $id,
        public int $apartmentId,
        public string $title,
        public string $description,
        public string $status,
        public ?string $response,
        public ?array $apartment,
        public ?string $createdAt,
        public ?string $updatedAt,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: (int) $data['id'],
            apartmentId: (int) $data['apartment_id'],
            title: (string) $data['title'],
            description: (string) $data['description'],
            status: (string) $data['status'],
            response: isset($data['response']) ? (string) $data['response'] : null,
            apartment: is_array($data['apartment'] ?? null) ? $data['apartment'] : null,
            createdAt: isset($data['created_at']) ? (string) $data['created_at'] : null,
            updatedAt: isset($data['updated_at']) ? (string) $data['updated_at'] : null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'apartment_id' => $this->apartmentId,
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status,
            'status_label' => $this->statusLabel(),
            'response' => $this->response,
            'apartment' => $this->apartment,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'processing' => 'در حال بررسی',
            'completed' => 'تکمیل‌شده',
            'rejected' => 'ردشده',
            default => 'در انتظار بررسی',
        };
    }
}
