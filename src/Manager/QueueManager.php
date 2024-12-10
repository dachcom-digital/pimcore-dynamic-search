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

    public function getQueueTableName(): string
    {
        return $this->tableName;
    }

    public function getTotalQueuedItems(): int
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->select('COUNT(id)')->from($this->tableName);

        return (int)$qb->executeQuery()->fetchOne();
    }

    public function clearQueue(): void
    {
        try {
            $affectedRows = $this->getTotalQueuedItems();
            $sql = $this->connection->getDatabasePlatform()->getTruncateTableSQL($this->tableName);
            $this->connection->executeStatement($sql);
            $this->logger->debug(sprintf('data queue cleared. Affected jobs: %d', $affectedRows), 'queue', 'default');
        } catch (\Throwable $e) {
            $this->logger->error(sprintf('Error while clearing queue. Message was: %s', $e->getMessage()), 'queue', 'default');
        }
    }
}
