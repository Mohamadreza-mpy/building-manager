<?php

namespace App\Services;

use App\Exceptions\ApiException;
use App\Models\Announcement;

class AnnouncementService
{
    public function __construct(private readonly ApiClient $api) {}

    /** @return list<Announcement> */
    public function all(int $buildingId): array
    {
        $response = $this->api->get("/buildings/{$buildingId}/announcements");

        if (! is_array($response->data)) {
            throw new ApiException('فهرست اطلاعیه‌ها از سرور دریافت نشد.');
        }

        return array_map(fn (array $item) => Announcement::fromArray($item), $response->data);
    }

    public function create(int $buildingId, array $data): Announcement
    {
        $response = $this->api->post("/buildings/{$buildingId}/announcements", $data);

        if (! is_array($response->data)) {
            throw new ApiException('اطلاعات اطلاعیه از سرور دریافت نشد.');
        }

        return Announcement::fromArray($response->data);
    }
}
