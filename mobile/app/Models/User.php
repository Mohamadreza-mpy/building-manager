<?php

namespace App\Models;

final readonly class User
{
    public function __construct(
        public int $id,
        public string $name,
        public string $mobile,
        public ?string $email,
        public string $role,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: (int) $data['id'],
            name: (string) $data['name'],
            mobile: (string) $data['mobile'],
            email: isset($data['email']) ? (string) $data['email'] : null,
            role: (string) $data['role'],
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'mobile' => $this->mobile,
            'email' => $this->email,
            'role' => $this->role,
        ];
    }

    public function roleLabel(): string
    {
        return match ($this->role) {
            'admin' => 'مدیر سامانه',
            'manager' => 'مدیر ساختمان',
            'owner' => 'مالک',
            'resident' => 'ساکن',
            default => 'کاربر',
        };
    }
}
