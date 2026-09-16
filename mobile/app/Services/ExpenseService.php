<?php

namespace App\Services;

use App\Exceptions\ApiException;
use App\Models\Expense;

class ExpenseService
{
    public function __construct(private readonly ApiClient $api) {}

    /** @return list<Expense> */
    public function all(int $buildingId): array
    {
        $response = $this->api->get("/buildings/{$buildingId}/expenses");

        if (! is_array($response->data)) {
            throw new ApiException('فهرست هزینه‌ها از سرور دریافت نشد.');
        }

        return array_map(fn (array $item) => Expense::fromArray($item), $response->data);
    }

    public function create(int $buildingId, array $data, ?string $filePath = null, ?string $mimeType = null, ?string $fileName = null): Expense
    {
        $response = $this->api->postMultipart("/buildings/{$buildingId}/expenses", $data, $filePath, $mimeType, $fileName);

        if (! is_array($response->data)) {
            throw new ApiException('اطلاعات هزینه از سرور دریافت نشد.');
        }

        return Expense::fromArray($response->data);
    }
}
