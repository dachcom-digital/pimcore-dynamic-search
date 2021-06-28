<?php

namespace DynamicSearchBundle\Service;

use Doctrine\DBAL\Connection;
use DynamicSearchBundle\Doctrine\DBAL\ConnectionKeepAlive;

class LongProcessService implements LongProcessServiceInterface
{
    protected Connection $connection;
    protected ?ConnectionKeepAlive $keepAlive = null;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function boot(): void
    {
        $this->keepAlive = new ConnectionKeepAlive();
        $this->keepAlive->addConnection($this->connection);
        $this->keepAlive->attach();
    }

    public function shutdown(): void
    {
        $this->keepAlive->detach();
    }
}
