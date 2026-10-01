<?php

namespace App\Services;

use App\Exceptions\ApiException;
use App\Models\Resident;

class ResidentService
{
    public function __construct(private readonly ApiClient $api) {}

    /** @return list<Resident> */
    public function all(?string $search = null): array
    {
        $response = $this->api->get('/residents', filled($search) ? ['search' => $search] : []);

        if (! is_array($response->data)) {
            throw new ApiException('فهرست ساکنین از سرور دریافت نشد.');
        }

        return array_map(fn (array $item) => Resident::fromArray($item), $response->data);
    }

    public function create(array $data): Resident
    {
        $response = $this->api->post('/residents', $data);

        if (! is_array($response->data)) {
            throw new ApiException('اطلاعات ساکن از سرور دریافت نشد.');
        }

        return Resident::fromArray($response->data);
    }
}
