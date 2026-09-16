<?php

namespace App\Services;

use App\Exceptions\ApiException;
use App\Models\Apartment;

class ApartmentService
{
    public function __construct(private readonly ApiClient $api) {}

    /** @return list<Apartment> */
    public function all(int $buildingId): array
    {
        $response = $this->api->get("/buildings/{$buildingId}/apartments");

        if (! is_array($response->data)) {
            throw new ApiException('فهرست واحدها از سرور دریافت نشد.');
        }

        return array_map(fn (array $item) => Apartment::fromArray($item), $response->data);
    }

    public function find(int $id): Apartment
    {
        return $this->apartmentFrom($this->api->get("/apartments/{$id}")->data);
    }

    public function create(int $buildingId, array $data): Apartment
    {
        return $this->apartmentFrom($this->api->post("/buildings/{$buildingId}/apartments", $data)->data);
    }

    public function update(int $id, array $data): Apartment
    {
        return $this->apartmentFrom($this->api->put("/apartments/{$id}", $data)->data);
    }

    public function delete(int $id): void
    {
        $this->api->delete("/apartments/{$id}");
    }

    private function apartmentFrom(mixed $data): Apartment
    {
        if (! is_array($data)) {
            throw new ApiException('اطلاعات واحد از سرور دریافت نشد.');
        }

        return Apartment::fromArray($data);
    }
}
