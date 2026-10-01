<?php

namespace App\Services;

use App\Exceptions\ApiException;
use App\Models\NotificationItem;

class NotificationService
{
    public function __construct(private readonly ApiClient $api) {}

    /** @return list<NotificationItem> */
    public function all(): array
    {
        $response = $this->api->get('/notifications');

        if (! is_array($response->data)) {
            throw new ApiException('فهرست اعلان‌ها از سرور دریافت نشد.');
        }

        return array_map(fn (array $item) => NotificationItem::fromArray($item), $response->data);
    }

    public function markAsRead(string $id): NotificationItem
    {
        $data = $this->api->put("/notifications/{$id}/read")->data;

        if (! is_array($data)) {
            throw new ApiException('وضعیت اعلان از سرور دریافت نشد.');
        }

        return NotificationItem::fromArray($data);
    }

    public function registerDevice(string $token): void
    {
        $this->api->post('/device-tokens', ['token' => $token]);
    }

    public function removeDevice(string $token): void
    {
        $this->api->delete('/device-tokens', ['token' => $token]);
    }
}
