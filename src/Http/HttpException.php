<?php

declare(strict_types=1);

namespace Pulsar\Framework\Http;

class HttpException extends \Exception
{
    private int $statusCode = 400;

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function setStatusCode(int $statusCode): void
    {
        $this->statusCode = $statusCode;
    }
}