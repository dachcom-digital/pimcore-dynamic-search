<?php

namespace DynamicSearchBundle\Doctrine\DBAL;

use Doctrine\DBAL\Connection;

declare(ticks=3000000);

class ConnectionKeepAlive
{
    protected array $connections;
    protected bool $isAttached;

    public function __construct()
    {
        $this->connections = [];
        $this->isAttached = false;
    }

    public function detach(): void
    {
        unregister_tick_function([$this, 'kick']);
        $this->isAttached = false;
    }

    public function attach(): void
    {
        if ($this->isAttached || register_tick_function([$this, 'kick'])) {
            $this->isAttached = true;

            return;
        }

        throw new \RuntimeException('Unable to attach keep alive to the system');
    }

    public function addConnection(Connection $logConnection): void
    {
        $this->connections[spl_object_hash($logConnection)] = $logConnection;
    }

    /**
     * @throws \Exception
     */
    public function kick(): void
    {
        foreach ($this->connections as $conn) {
            try {
                $conn->executeQuery('SELECT 1')->free();
            } catch (\Exception $e) {
                if ($conn === null || stripos($e->getMessage(), 'SQLSTATE[HY000]: General error: 2006 MySQL server has gone away') === false) {
                    throw $e;
                }
                $conn->close();
                $conn->connect();
            }
        }
    }
}
