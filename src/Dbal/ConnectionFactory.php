<?php

declare(strict_types=1);

namespace Pulsar\Framework\Dbal;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;

class ConnectionFactory
{
    public function __construct(private array $connectionParams)
    {
    }

    public function create(): Connection
    {
        return DriverManager::getConnection($this->connectionParams);
    }
}