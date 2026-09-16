<?php

namespace App\Services;

use App\Exceptions\ApiException;
use App\Models\Building;

class BuildingService
{
    public function __construct(private readonly ApiClient $api) {}

    /** @return list<Building> */
    public function all(): array
    {
        $response = $this->api->get('/buildings');

        if (! is_array($response->data)) {
            throw new ApiException('فهرست ساختمان‌ها از سرور دریافت نشد.');
        }

        return array_map(fn (array $item) => Building::fromArray($item), $response->data);
    }

    public function find(int $id): Building
    {
        return $this->buildingFrom($this->api->get("/buildings/{$id}")->data);
    }

    public function create(array $data): Building
    {
        return $this->buildingFrom($this->api->post('/buildings', $data)->data);
    }

    public function update(int $id, array $data): Building
    {
        return $this->buildingFrom($this->api->put("/buildings/{$id}", $data)->data);
    }

    public function delete(int $id): void
    {
        $this->api->delete("/buildings/{$id}");
    }

    private function buildingFrom(mixed $data): Building
    {
        if (! is_array($data)) {
            throw new ApiException('اطلاعات ساختمان از سرور دریافت نشد.');
        }

        return Building::fromArray($data);
    }
}
