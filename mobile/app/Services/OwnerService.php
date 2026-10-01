<?php

namespace App\Services;

use App\Exceptions\ApiException;
use App\Models\Owner;

class OwnerService
{
    public function __construct(private readonly ApiClient $api) {}

    /** @return list<Owner> */
    public function all(?string $search = null): array
    {
        $response = $this->api->get('/owners', filled($search) ? ['search' => $search] : []);

        if (! is_array($response->data)) {
            throw new ApiException('فهرست مالکین از سرور دریافت نشد.');
        }

        return array_map(fn (array $item) => Owner::fromArray($item), $response->data);
    }

    public function create(array $data): Owner
    {
        $response = $this->api->post('/owners', $data);

        if (! is_array($response->data)) {
            throw new ApiException('اطلاعات مالک از سرور دریافت نشد.');
        }

        return Owner::fromArray($response->data);
    }
}
