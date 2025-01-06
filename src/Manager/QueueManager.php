<?php

/*
 * This source file is available under two different licenses:
 *   - GNU General Public License version 3 (GPLv3)
 *   - DACHCOM Commercial License (DCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) DACHCOM.DIGITAL AG (https://www.dachcom-digital.com)
 * @license    GPLv3 and DCL
 */

namespace DynamicSearchBundle\Manager;

use Doctrine\DBAL\Connection;
use DynamicSearchBundle\Logger\LoggerInterface;

class QueueManager implements QueueManagerInterface
{
    public function __construct(
        protected LoggerInterface $logger,
        protected Connection $connection,
        protected string $tableName
    ) {
    }

    public function getQueueTableName(): string
    {
        return $this->tableName;
    }

    public function getTotalQueuedItems(): int
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->select('COUNT(id)')->from($this->tableName);

        return (int) $qb->executeQuery()->fetchOne();
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
