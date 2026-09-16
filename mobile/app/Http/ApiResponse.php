<?php

namespace App\Http;

final readonly class ApiResponse
{
    public function __construct(
        public bool $success,
        public string $message,
        public mixed $data,
        public array $errors = [],
        public int $status = 200,
    ) {}
}
