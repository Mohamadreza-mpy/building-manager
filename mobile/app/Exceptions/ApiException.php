<?php

namespace App\Exceptions;

use RuntimeException;

class ApiException extends RuntimeException
{
    public function __construct(
        string $message,
        public readonly array $errors = [],
        public readonly int $status = 0,
    ) {
        parent::__construct($message);
    }
}
