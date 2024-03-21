<?php

namespace DynamicSearchBundle\Queue\Transport;

use Doctrine\DBAL\Connection as DBALConnection;
use Doctrine\DBAL\Driver\Exception as DriverException;
use Doctrine\DBAL\Exception as DBALException;
use Doctrine\DBAL\Exception\TableNotFoundException;
use Doctrine\DBAL\LockMode;
use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Platforms\OraclePlatform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Result;
use Doctrine\DBAL\Schema\Synchronizer\SchemaSynchronizer;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Messenger\Bridge\Doctrine\Transport\Connection;
use Symfony\Component\Messenger\Exception\TransportException;

final class ExtendedDoctrineConnection extends Connection
{
    private bool $autoSetup;

    public function __construct(array $configuration, DBALConnection $driverConnection, ?SchemaSynchronizer $schemaSynchronizer = null)
    {
        parent::__construct($configuration, $driverConnection, $schemaSynchronizer);
        $this->autoSetup = $this->configuration['auto_setup'];
    }

    public function get(): ?array
    {
        if ($this->driverConnection->getDatabasePlatform() instanceof MySQLPlatform) {
            try {
                $this->driverConnection->delete($this->configuration['table_name'], ['delivered_at' => '9999-12-31 23:59:59']);
            } catch (DriverException $e) {
                // Ignore the exception
            }
        }

        get:
        $this->driverConnection->beginTransaction();
        try {
            $query = $this->createAvailableMessagesQueryBuilder()
                ->orderBy('available_at_micro', 'ASC')
                ->setMaxResults(1);

            if ($this->driverConnection->getDatabasePlatform() instanceof OraclePlatform) {
                $query->select('m.id');
            }

            // Append pessimistic write lock to FROM clause if db platform supports it
            $sql = $query->getSQL();

            // Wrap the rownum query in a sub-query to allow writelocks without ORA-02014 error
            if ($this->driverConnection->getDatabasePlatform() instanceof OraclePlatform) {
                $query = $this->createQueryBuilder('w')
                    ->where('w.id IN ('.str_replace('SELECT a.* FROM', 'SELECT a.id FROM', $sql).')')
                    ->setParameters($query->getParameters(), $query->getParameterTypes());

                if (method_exists(QueryBuilder::class, 'forUpdate')) {
                    $query->forUpdate();
                }

                $sql = $query->getSQL();
            } elseif (method_exists(QueryBuilder::class, 'forUpdate')) {
                $query->forUpdate();
                try {
                    $sql = $query->getSQL();
                } catch (DBALException $e) {
                }
            } elseif (preg_match('/FROM (.+) WHERE/', (string) $sql, $matches)) {
                $fromClause = $matches[1];
                $sql = str_replace(
                    sprintf('FROM %s WHERE', $fromClause),
                    sprintf('FROM %s WHERE', $this->driverConnection->getDatabasePlatform()->appendLockHint($fromClause, LockMode::PESSIMISTIC_WRITE)),
                    $sql
                );
            }

            // use SELECT ... FOR UPDATE to lock table
            if (!method_exists(QueryBuilder::class, 'forUpdate')) {
                $sql .= ' '.$this->driverConnection->getDatabasePlatform()->getWriteLockSQL();
            }

            $stmt = $this->executeQuery(
                $sql,
                $query->getParameters(),
                $query->getParameterTypes()
            );
            $doctrineEnvelope = $stmt instanceof Result ? $stmt->fetchAssociative() : $stmt->fetch();

            if (false === $doctrineEnvelope) {
                $this->driverConnection->commit();
                $this->queueEmptiedAt = microtime(true) * 1000;

                return null;
            }
            // Postgres can "group" notifications having the same channel and payload
            // We need to be sure to empty the queue before blocking again
            $this->queueEmptiedAt = null;

            $doctrineEnvelope = $this->decodeEnvelopeHeaders($doctrineEnvelope);

            $queryBuilder = $this->driverConnection->createQueryBuilder()
                ->update($this->configuration['table_name'])
                ->set('delivered_at', '?')
                ->where('id = ?');
            $now = new \DateTimeImmutable('UTC');
            $this->executeStatement($queryBuilder->getSQL(), [
                $now,
                $doctrineEnvelope['id'],
            ], [
                Types::DATETIME_IMMUTABLE,
            ]);

            $this->driverConnection->commit();

            return $doctrineEnvelope;
        } catch (\Throwable $e) {
            $this->driverConnection->rollBack();

            if ($this->autoSetup && $e instanceof TableNotFoundException) {
                $this->setup();
                goto get;
            }

            throw $e;
        }
    }

    public function send(string $body, array $headers, int $delay = 0): string
    {
        $now = new \DateTimeImmutable('UTC');
        $availableAt = $now->modify(sprintf('%+d seconds', $delay / 1000));
        $availableAtMicro = ((int)$availableAt->format('U') * 1000000) + (int)$availableAt->format('u');

        $queryBuilder = $this->driverConnection->createQueryBuilder()
            ->insert($this->configuration['table_name'])
            ->values([
                'body' => '?',
                'headers' => '?',
                'queue_name' => '?',
                'created_at' => '?',
                'available_at' => '?',
                'available_at_micro' => '?'
            ]);

        return $this->executeInsert($queryBuilder->getSQL(), [
            $body,
            json_encode($headers),
            $this->configuration['queue_name'],
            $now,
            $availableAt,
            $availableAtMicro
        ], [
            Types::STRING,
            Types::STRING,
            Types::STRING,
            Types::DATETIME_IMMUTABLE,
            Types::DATETIME_IMMUTABLE,
            Types::INTEGER
        ]);
    }

    private function createAvailableMessagesQueryBuilder(): QueryBuilder
    {
        $now = new \DateTimeImmutable('UTC');
        $redeliverLimit = $now->modify(sprintf('-%d seconds', $this->configuration['redeliver_timeout']));

        return $this->createQueryBuilder()
            ->where('m.queue_name = ?')
            ->andWhere('m.delivered_at is null OR m.delivered_at < ?')
            ->andWhere('m.available_at <= ?')
            ->setParameters([
                $this->configuration['queue_name'],
                $redeliverLimit,
                $now,
            ], [
                Types::STRING,
                Types::DATETIME_IMMUTABLE,
                Types::DATETIME_IMMUTABLE,
            ]);
    }

    private function createQueryBuilder(string $alias = 'm'): QueryBuilder
    {
        $queryBuilder = $this->driverConnection->createQueryBuilder()
            ->from($this->configuration['table_name'], $alias);

        $alias .= '.';

        if (!$this->driverConnection->getDatabasePlatform() instanceof OraclePlatform) {
            return $queryBuilder->select($alias.'*');
        }

        // Oracle databases use UPPER CASE on tables and column identifiers.
        // Column alias is added to force the result to be lowercase even when the actual field is all caps.

        return $queryBuilder->select(str_replace(', ', ', '.$alias,
            $alias.'id AS "id", body AS "body", headers AS "headers", queue_name AS "queue_name", '.
            'created_at AS "created_at", available_at AS "available_at", '.
            'delivered_at AS "delivered_at", available_at_micro AS "available_at_micro"'
        ));
    }

    private function executeQuery(string $sql, array $parameters = [], array $types = []): Result|AbstractionResult|ResultStatement
    {
        try {
            $stmt = $this->driverConnection->executeQuery($sql, $parameters, $types);
        } catch (TableNotFoundException $e) {
            if (!$this->autoSetup || $this->driverConnection->isTransactionActive()) {
                throw $e;
            }

            $this->setup();

            $stmt = $this->driverConnection->executeQuery($sql, $parameters, $types);
        }

        return $stmt;
    }

    private function executeInsert(string $sql, array $parameters = [], array $types = []): string
    {
        // Use PostgreSQL RETURNING clause instead of lastInsertId() to get the
        // inserted id in one operation instead of two.
        if ($this->driverConnection->getDatabasePlatform() instanceof PostgreSQLPlatform) {
            $sql .= ' RETURNING id';
        }

        insert:
        $this->driverConnection->beginTransaction();

        try {
            if ($this->driverConnection->getDatabasePlatform() instanceof PostgreSQLPlatform) {
                $first = $this->driverConnection->fetchFirstColumn($sql, $parameters, $types);

                $id = $first[0] ?? null;

                if (!$id) {
                    throw new TransportException('no id was returned by PostgreSQL from RETURNING clause.');
                }
            } else {
                $this->driverConnection->executeStatement($sql, $parameters, $types);

                if (!$id = $this->driverConnection->lastInsertId()) {
                    throw new TransportException('lastInsertId() returned false, no id was returned.');
                }
            }

            $this->driverConnection->commit();
        } catch (\Throwable $e) {
            $this->driverConnection->rollBack();

            // handle setup after transaction is no longer open
            if ($this->autoSetup && $e instanceof TableNotFoundException) {
                $this->setup();
                goto insert;
            }

            throw $e;
        }

        return $id;
    }

    private function decodeEnvelopeHeaders(array $doctrineEnvelope): array
    {
        $doctrineEnvelope['headers'] = json_decode($doctrineEnvelope['headers'], true);

        return $doctrineEnvelope;
    }

    public function getExtraSetupSqlForTable(Table $createdTable): array
    {
        if (!$createdTable->hasOption(self::TABLE_OPTION_NAME)) {
            return [];
        }

        if ($createdTable->getOption(self::TABLE_OPTION_NAME) !== $this->configuration['table_name']) {
            return [];
        }

        return $this->getAdditionalColumnSql();
    }

    private function getAdditionalColumnSql(): array
    {
        return [
            sprintf('ALTER TABLE %s ADD available_at_micro BIGINT UNSIGNED;', $this->configuration['table_name']),
            sprintf('CREATE INDEX IDX_available_at_micro ON %s (available_at_micro);', $this->configuration['table_name'])
        ];
    }

}
