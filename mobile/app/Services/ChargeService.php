<?php

namespace App\Services;

use App\Exceptions\ApiException;
use App\Models\Charge;

class ChargeService
{
    public function __construct(private readonly ApiClient $api) {}

    /** @return list<Charge> */
    public function forBuilding(int $buildingId): array
    {
        return $this->chargeList($this->api->get("/buildings/{$buildingId}/charges")->data);
    }

    /** @return list<Charge> */
    public function mine(): array
    {
        return $this->chargeList($this->api->get('/charges')->data);
    }

    public function find(int $id): Charge
    {
        return $this->chargeFrom($this->api->get("/charges/{$id}")->data);
    }

    public function create(int $buildingId, array $data): Charge
    {
        return $this->chargeFrom($this->api->post("/buildings/{$buildingId}/charges", $data)->data);
    }

    public function submitReceipt(int $id, string $filePath, ?string $mimeType = null, ?string $fileName = null): Charge
    {
        return $this->chargeFrom($this->api->postMultipart("/charges/{$id}/receipt", [], $filePath, $mimeType, $fileName)->data);
    }

    private function chargeList(mixed $data): array
    {
        if (! is_array($data)) {
            throw new ApiException('فهرست شارژها از سرور دریافت نشد.');
        }

        return array_map(fn (array $item) => Charge::fromArray($item), $data);
    }

    private function chargeFrom(mixed $data): Charge
    {
        if (! is_array($data)) {
            throw new ApiException('اطلاعات شارژ از سرور دریافت نشد.');
        }

        return Charge::fromArray($data);
    }
}
