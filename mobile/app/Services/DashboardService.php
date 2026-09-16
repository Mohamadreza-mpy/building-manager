<?php

namespace App\Services;

use App\Exceptions\ApiException;
use App\Models\DashboardData;

class DashboardService
{
    public function __construct(private readonly ApiClient $api) {}

    public function load(): DashboardData
    {
        $response = $this->api->get('/dashboard');

        if (! is_array($response->data)) {
            throw new ApiException('اطلاعات داشبورد از سرور دریافت نشد.');
        }

        return DashboardData::fromArray($response->data);
    }
}
