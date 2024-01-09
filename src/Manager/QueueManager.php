<?php

namespace DynamicSearchBundle\Manager;

use Doctrine\DBAL\Connection;
use DynamicSearchBundle\Logger\LoggerInterface;

class QueueManager implements QueueManagerInterface
{
    public function __construct(
        protected LoggerInterface $logger,
        protected Connection $connection,
        protected string $tableName
    )
    {}

    public function clearQueue(): void
    {
        try {
            $stmt = sprintf('SELECT COUNT(*) FROM %s', $this->tableName);
            $affectedRows = $this->connection->executeQuery($stmt)->fetchFirstColumn();
            $sql = $this->connection->getDatabasePlatform()->getTruncateTableSQL($this->tableName);
            $this->connection->executeStatement($sql);
            $this->logger->debug(sprintf('data queue cleared. Affected jobs: %d', $affectedRows), 'queue', 'maintenance');
        } catch (\Throwable $e) {
            $this->logger->error(sprintf('Error while clearing queue. Message was: %s', $e->getMessage()), 'queue', 'maintenance');
        }
    }
}
