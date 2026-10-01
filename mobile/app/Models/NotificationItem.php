<?php

namespace App\Models;

final readonly class NotificationItem
{
    public function __construct(
        public string $id,
        public ?string $type,
        public string $title,
        public string $body,
        public array $meta,
        public ?string $readAt,
        public ?string $createdAt,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: (string) $data['id'],
            type: isset($data['type']) ? (string) $data['type'] : null,
            title: (string) ($data['title'] ?? 'اعلان جدید'),
            body: (string) ($data['body'] ?? ''),
            meta: is_array($data['meta'] ?? null) ? $data['meta'] : [],
            readAt: isset($data['read_at']) ? (string) $data['read_at'] : null,
            createdAt: isset($data['created_at']) ? (string) $data['created_at'] : null,
        );
    }

    public function toArray(): array
    {
        return ['id' => $this->id, 'type' => $this->type, 'title' => $this->title, 'body' => $this->body, 'meta' => $this->meta, 'read_at' => $this->readAt, 'created_at' => $this->createdAt];
    }
}
