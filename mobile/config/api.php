<?php

return [
    'base_url' => rtrim((string) env('API_BASE_URL', 'http://127.0.0.1:8000/api'), '/'),
    'timeout' => (int) env('API_TIMEOUT', 15),
];
