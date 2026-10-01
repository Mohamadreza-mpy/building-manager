<?php

namespace App\Services;

use App\Exceptions\ApiException;
use App\Models\ResidentRequest;

class ResidentRequestService
{
    public function __construct(private readonly ApiClient $api) {}

    /** @return list<ResidentRequest> */
    public function all(): array
    {
        $response = $this->api->get('/requests');

        if (! is_array($response->data)) {
            throw new ApiException('فهرست درخواست‌ها از سرور دریافت نشد.');
        }

        return array_map(fn (array $item) => ResidentRequest::fromArray($item), $response->data);
    }

    public function find(int $id): ResidentRequest
    {
        foreach ($this->all() as $item) {
            if ($item->id === $id) {
                return $item;
            }
        }

        throw new ApiException('درخواست موردنظر پیدا نشد.', status: 404);
    }

    public function create(array $data): ResidentRequest
    {
        return $this->requestFrom($this->api->post('/requests', $data)->data);
    }

    public function respond(int $id, array $data): ResidentRequest
    {
        return $this->requestFrom($this->api->put("/requests/{$id}", $data)->data);
    }

    private function requestFrom(mixed $data): ResidentRequest
    {
        if (! is_array($data)) {
            throw new ApiException('اطلاعات درخواست از سرور دریافت نشد.');
        }

        return ResidentRequest::fromArray($data);
    }
}
