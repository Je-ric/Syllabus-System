<?php

namespace App\Exceptions;

use RuntimeException;
use Throwable;

class CaisApiException extends RuntimeException
{
    public function __construct(
        string $message,
        private readonly int $statusCode = 0,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $statusCode, $previous);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function isNotFound(): bool
    {
        return $this->statusCode === 404;
    }

    public function isUnauthorized(): bool
    {
        return $this->statusCode === 401;
    }

    public function isUnavailable(): bool
    {
        return $this->statusCode === 0 || $this->statusCode >= 500;
    }
}
