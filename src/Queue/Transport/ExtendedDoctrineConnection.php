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

namespace DynamicSearchBundle\Queue\Transport;

use Doctrine\DBAL\Abstraction\Result as AbstractionResult;
use Doctrine\DBAL\Connection as DBALConnection;
use Doctrine\DBAL\Driver\Exception as DriverException;
use Doctrine\DBAL\Driver\ResultStatement;
use Doctrine\DBAL\Exception as DBALException;
use Doctrine\DBAL\Exception\TableNotFoundException;
use Doctrine\DBAL\LockMode;
use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Platforms\OraclePlatform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Query\ForUpdate\ConflictResolutionMode;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Result;
use Doctrine\DBAL\Schema\AbstractAsset;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Satag\DoctrineFirebirdDriver\Platforms\FirebirdPlatform;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\Comparator;
use Doctrine\DBAL\Schema\ComparatorConfig;
use Doctrine\DBAL\Schema\Index;
use Doctrine\DBAL\Schema\Name\Identifier;
use Doctrine\DBAL\Schema\Name\UnqualifiedName;
use Doctrine\DBAL\Schema\PrimaryKeyConstraint;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\SchemaDiff;
use Doctrine\DBAL\Schema\Sequence;
use Doctrine\DBAL\Schema\Synchronizer\SchemaSynchronizer;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Messenger\Bridge\Doctrine\Transport\Connection;
use Symfony\Component\Messenger\Exception\TransportException;

final class ExtendedDoctrineConnection extends Connection
{
    private const ORACLE_SEQUENCES_SUFFIX = '_seq';

    private bool $autoSetup;
    private ?SchemaSynchronizer $schemaSynchronizer;

    public function __construct(array $configuration, DBALConnection $driverConnection, ?SchemaSynchronizer $schemaSynchronizer = null)
    {
        parent::__construct($configuration, $driverConnection, $schemaSynchronizer);
        $this->autoSetup = $this->configuration['auto_setup'];
        $this->schemaSynchronizer = $schemaSynchronizer;
    }

    public function setup(): void
    {
        $configuration = $this->driverConnection->getConfiguration();
        $assetFilter = $configuration->getSchemaAssetsFilter();
        $configuration->setSchemaAssetsFilter(function ($tableName) {
            if ($tableName instanceof AbstractAsset) {
                $tableName = $tableName->getName();
            }

            if (!\is_string($tableName)) {
                throw new \TypeError(\sprintf('The table name must be an instance of "%s" or a string ("%s" given).', AbstractAsset::class, get_debug_type($tableName)));
            }

            return $tableName === $this->configuration['table_name'];
        });
        $this->updateSchema();
        $configuration->setSchemaAssetsFilter($assetFilter);
        $this->autoSetup = false;
    }

    public function configureSchema(Schema $schema, DBALConnection $forConnection, \Closure $isSameDatabase): Schema
    {
        if ($schema->hasTable($this->configuration['table_name'])) {
            return $schema;
        }

        if ($forConnection !== $this->driverConnection && !$isSameDatabase($this->executeStatement(...))) {
            return $schema;
        }

        return $this->addTableToSchema($schema);
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

            $sql = $query->getSQL();

            if ($this->driverConnection->getDatabasePlatform() instanceof OraclePlatform) {
                $query = $this->createQueryBuilder('w')
                    ->where('w.id IN (' . str_replace('SELECT a.* FROM', 'SELECT a.id FROM', $sql) . ')')
                    ->setParameters($query->getParameters(), $query->getParameterTypes());

                if (method_exists(QueryBuilder::class, 'forUpdate')) {
                    $query->forUpdate(ConflictResolutionMode::SKIP_LOCKED);
                }

                $sql = $query->getSQL();
            } elseif (method_exists(QueryBuilder::class, 'forUpdate')) {
                $query->forUpdate(ConflictResolutionMode::SKIP_LOCKED);

                try {
                    $sql = $query->getSQL();
                } catch (DBALException $e) {
                    $query->forUpdate();

                    try {
                        $sql = $query->getSQL();
                    } catch (DBALException $e) {
                    }
                }
            } elseif (preg_match('/FROM (.+) WHERE/', (string) $sql, $matches)) {
                $fromClause = $matches[1];
                $sql = str_replace(
                    \sprintf('FROM %s WHERE', $fromClause),
                    \sprintf('FROM %s WHERE', $this->driverConnection->getDatabasePlatform()->appendLockHint($fromClause, LockMode::PESSIMISTIC_WRITE)),
                    $sql
                );
            }

            if (!method_exists(QueryBuilder::class, 'forUpdate')) {
                $sql .= ' ' . $this->driverConnection->getDatabasePlatform()->getWriteLockSQL();
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
        $availableAt = $now->modify(\sprintf('%+d seconds', $delay / 1000));
        $availableAtMicro = ((int) $availableAt->format('U') * 1000000) + (int) $availableAt->format('u');

        $queryBuilder = $this->driverConnection->createQueryBuilder()
            ->insert($this->configuration['table_name'])
            ->values([
                'body'               => '?',
                'headers'            => '?',
                'queue_name'         => '?',
                'created_at'         => '?',
                'available_at'       => '?',
                'available_at_micro' => '?',
            ]);

        return $this->executeInsert($queryBuilder->getSQL(), [
            $body,
            json_encode($headers),
            $this->configuration['queue_name'],
            $now,
            $availableAt,
            $availableAtMicro,
        ], [
            Types::STRING,
            Types::STRING,
            Types::STRING,
            Types::DATETIME_IMMUTABLE,
            Types::DATETIME_IMMUTABLE,
            Types::BIGINT,
        ]);
    }

    private function createAvailableMessagesQueryBuilder(): QueryBuilder
    {
        $now = new \DateTimeImmutable('UTC');
        $redeliverLimit = $now->modify(\sprintf('-%d seconds', $this->configuration['redeliver_timeout']));

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

        if (!$this->driverConnection->getDatabasePlatform() instanceof FirebirdPlatform
            && !$this->driverConnection->getDatabasePlatform() instanceof OraclePlatform
        ) {
            return $queryBuilder->select($alias . '*');
        }

        // Oracle/Firebird use UPPER CASE identifiers — aliases force lowercase results
        return $queryBuilder->select(str_replace(
            ', ',
            ', ' . $alias,
            $alias . 'id AS "id", body AS "body", headers AS "headers", queue_name AS "queue_name", ' .
            'created_at AS "created_at", available_at AS "available_at", ' .
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
        if ($this->driverConnection->getDatabasePlatform() instanceof PostgreSQLPlatform) {
            $sql .= ' RETURNING id';
        }

        insert:
        $this->driverConnection->beginTransaction();

        try {
            if ($this->driverConnection->getDatabasePlatform() instanceof PostgreSQLPlatform) {
                if (!$id = $this->driverConnection->fetchFirstColumn($sql, $parameters, $types)[0] ?? null) {
                    throw new TransportException('no id was returned by PostgreSQL from RETURNING clause.');
                }

                $this->driverConnection->executeStatement('SELECT pg_notify(?, ?)', [$this->configuration['table_name'], $this->configuration['queue_name']]);
            } elseif ($this->driverConnection->getDatabasePlatform() instanceof OraclePlatform) {
                $sequenceName = $this->configuration['table_name'] . self::ORACLE_SEQUENCES_SUFFIX;

                $this->driverConnection->executeStatement($sql, $parameters, $types);

                if (!$id = (int) $this->driverConnection->fetchOne('SELECT ' . $sequenceName . '.CURRVAL FROM DUAL')) {
                    throw new TransportException('no id was returned by Oracle from sequence: ' . $sequenceName);
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

            if ($this->autoSetup && $e instanceof TableNotFoundException) {
                $this->setup();
                goto insert;
            }

            throw $e;
        }

        return $id;
    }

    private function getSchema(): Schema
    {
        return $this->addTableToSchema(new Schema([], [], $this->createSchemaManager()->createSchemaConfig()));
    }

    private function addTableToSchema(Schema $schema): Schema
    {
        $oracleSequenceName = null;
        $idOptions = ['autoincrement' => true, 'notnull' => true];

        if ($this->driverConnection->getDatabasePlatform() instanceof OraclePlatform) {
            $serverVersion = $this->driverConnection->executeQuery("SELECT version FROM product_component_version WHERE product LIKE 'Oracle Database%'")->fetchOne();
            if (version_compare($serverVersion, '12.1.0', '>=')) {
                $oracleSequenceName = $this->configuration['table_name'] . self::ORACLE_SEQUENCES_SUFFIX;
                $idOptions = ['autoincrement' => false, 'notnull' => true, 'default' => $oracleSequenceName . '.nextval'];
            }
        }

        if (method_exists($schema, 'edit')) {
            $editor = $schema->edit()->addTable($this->buildSchemaTable($oracleSequenceName));
            if (null !== $oracleSequenceName) {
                $editor->addSequence(new Sequence($oracleSequenceName));
            }

            return $editor->create();
        }

        $this->configureSchemaTable($schema->createTable($this->configuration['table_name']), $idOptions);

        if (null !== $oracleSequenceName) {
            $schema->createSequence($oracleSequenceName);
        }

        return $schema;
    }

    private function buildSchemaTable(?string $oracleSequenceName): Table
    {
        $idEditor = Column::editor()->setUnquotedName('id')->setTypeName(Types::BIGINT)->setNotNull(true);

        if (null !== $oracleSequenceName) {
            $idEditor->setAutoincrement(false)->setDefaultValue($oracleSequenceName . '.nextval');
        } else {
            $idEditor->setAutoincrement(true);
        }

        return Table::editor()
            ->setUnquotedName($this->configuration['table_name'])
            ->setOptions([self::TABLE_OPTION_NAME => $this->configuration['table_name']])
            ->addColumn($idEditor->create())
            ->addColumn(Column::editor()->setUnquotedName('body')->setTypeName(Types::TEXT)->setNotNull(true)->create())
            ->addColumn(Column::editor()->setUnquotedName('headers')->setTypeName(Types::TEXT)->setNotNull(true)->create())
            ->addColumn(Column::editor()->setUnquotedName('queue_name')->setTypeName(Types::STRING)->setLength(190)->setNotNull(true)->create())
            ->addColumn(Column::editor()->setUnquotedName('created_at')->setTypeName(Types::DATETIME_IMMUTABLE)->setNotNull(true)->create())
            ->addColumn(Column::editor()->setUnquotedName('available_at')->setTypeName(Types::DATETIME_IMMUTABLE)->setNotNull(true)->create())
            ->addColumn(Column::editor()->setUnquotedName('delivered_at')->setTypeName(Types::DATETIME_IMMUTABLE)->setNotNull(false)->create())
            ->addColumn(Column::editor()->setUnquotedName('available_at_micro')->setTypeName(Types::BIGINT)->setNotNull(true)->create())
            ->addPrimaryKeyConstraint(new PrimaryKeyConstraint(null, [new UnqualifiedName(Identifier::unquoted('id'))], true))
            ->addIndex(Index::editor()->setUnquotedColumnNames('queue_name', 'available_at', 'delivered_at', 'id'))
            ->addIndex(Index::editor()->setUnquotedColumnNames('available_at_micro'))
            ->create();
    }

    /**
     * To be removed when doctrine/dbal minimum is bumped to ^4.5.
     *
     * @param array<string, mixed> $idOptions
     */
    private function configureSchemaTable(Table $table, array $idOptions): void
    {
        $table->addOption(self::TABLE_OPTION_NAME, $this->configuration['table_name']);
        $table->addColumn('id', Types::BIGINT, $idOptions);
        $table->addColumn('body', Types::TEXT, ['notnull' => true]);
        $table->addColumn('headers', Types::TEXT, ['notnull' => true]);
        $table->addColumn('queue_name', Types::STRING, ['length' => 190, 'notnull' => true]);
        $table->addColumn('created_at', Types::DATETIME_IMMUTABLE, ['notnull' => true]);
        $table->addColumn('available_at', Types::DATETIME_IMMUTABLE, ['notnull' => true]);
        $table->addColumn('delivered_at', Types::DATETIME_IMMUTABLE, ['notnull' => false]);
        $table->addColumn('available_at_micro', Types::BIGINT, ['notnull' => true]);
        if (class_exists(PrimaryKeyConstraint::class)) {
            $table->addPrimaryKeyConstraint(new PrimaryKeyConstraint(null, [new UnqualifiedName(Identifier::unquoted('id'))], true));
        } else {
            $table->setPrimaryKey(['id']);
        }
        $table->addIndex(['queue_name', 'available_at', 'delivered_at', 'id']);
        $table->addIndex(['available_at_micro']);
    }

    private function decodeEnvelopeHeaders(array $doctrineEnvelope): array
    {
        $doctrineEnvelope['headers'] = json_decode($doctrineEnvelope['headers'], true);

        return $doctrineEnvelope;
    }

    private function updateSchema(): void
    {
        if (null !== $this->schemaSynchronizer) {
            $this->schemaSynchronizer->updateSchema($this->getSchema(), true);

            return;
        }

        $schemaManager = $this->createSchemaManager();
        $comparator = $this->createComparator($schemaManager);
        $schemaDiff = $this->compareSchemas($comparator, method_exists($schemaManager, 'introspectSchema') ? $schemaManager->introspectSchema() : $schemaManager->createSchema(), $this->getSchema());
        $platform = $this->driverConnection->getDatabasePlatform();

        if (!method_exists(SchemaDiff::class, 'getCreatedSchemas')) {
            foreach ($schemaDiff->toSaveSql($platform) as $sql) {
                $this->driverConnection->executeStatement($sql);
            }

            return;
        }

        if ($platform->supportsSchemas()) {
            foreach ($schemaDiff->getCreatedSchemas() as $schema) {
                $this->driverConnection->executeStatement($platform->getCreateSchemaSQL($schema));
            }
        }

        if ($platform->supportsSequences()) {
            foreach ($schemaDiff->getAlteredSequences() as $sequence) {
                $this->driverConnection->executeStatement($platform->getAlterSequenceSQL($sequence));
            }

            foreach ($schemaDiff->getCreatedSequences() as $sequence) {
                $this->driverConnection->executeStatement($platform->getCreateSequenceSQL($sequence));
            }
        }

        foreach ($platform->getCreateTablesSQL($schemaDiff->getCreatedTables()) as $sql) {
            $this->driverConnection->executeStatement($sql);
        }

        foreach ($schemaDiff->getAlteredTables() as $tableDiff) {
            foreach ($platform->getAlterTableSQL($tableDiff) as $sql) {
                $this->driverConnection->executeStatement($sql);
            }
        }
    }

    private function createSchemaManager(): AbstractSchemaManager
    {
        return method_exists($this->driverConnection, 'createSchemaManager')
            ? $this->driverConnection->createSchemaManager()
            : $this->driverConnection->getSchemaManager();
    }

    private function createComparator(AbstractSchemaManager $schemaManager): Comparator
    {
        if (class_exists(ComparatorConfig::class)) {
            return $schemaManager->createComparator((new ComparatorConfig())->withReportModifiedIndexes(false));
        }

        return method_exists($schemaManager, 'createComparator')
            ? $schemaManager->createComparator()
            : new Comparator();
    }

    private function compareSchemas(Comparator $comparator, Schema $from, Schema $to): SchemaDiff
    {
        return method_exists($comparator, 'compareSchemas') || method_exists($comparator, 'doCompareSchemas')
            ? $comparator->compareSchemas($from, $to)
            : $comparator->compare($from, $to);
    }
}