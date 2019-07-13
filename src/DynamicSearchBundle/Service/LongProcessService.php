<?php

namespace DynamicSearchBundle\Service;

use Doctrine\DBAL\Connection;
use DynamicSearchBundle\Doctrine\DBAL\ConnectionKeepAlive;

class LongProcessService implements LongProcessServiceInterface
{
    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @var ConnectionKeepAlive
     */
    protected $keepAlive;

    /**
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function boot()
    {
        $this->keepAlive = new ConnectionKeepAlive();
        $this->keepAlive->addConnection($this->connection);
        $this->keepAlive->attach();
    }

    public function shutdown()
    {
        $this->keepAlive->detach();
    }
}
