<?php
/**
 * Copyright (c) Reach Digital (https://www.reachdigital.nl/)
 * See README.md for license details.
 */

declare(strict_types=1);

namespace ReachDigital\ProophEventStore\Infrastructure\EventStore;

use Iterator;
use Prooph\Common\Messaging\MessageConverter;
use Prooph\EventStore\Pdo\DefaultMessageConverter;
use Prooph\EventStore\Pdo\HasQueryHint;
use Prooph\EventStore\Pdo\MariaDBIndexedPersistenceStrategy;
use Prooph\EventStore\Pdo\PersistenceStrategy;
use Prooph\EventStore\StreamName;

/**
 * Class MySqlSingleStreamStrategy
 * @see \Prooph\EventStore\Pdo\PersistenceStrategy\MySqlSingleStreamStrategy
 *  - Implements a more sane table name, instead of the has currently being used.
 *
 * @package ReachDigital\ProophEventStore\PdoEventStore\PersistenceStrategy
 */
final class MariaDbSingleStreamStrategy implements PersistenceStrategy, HasQueryHint, MariaDBIndexedPersistenceStrategy
{
    /**
     * @var PersistenceStrategy\MySqlSingleStreamStrategy
     */
    private $streamStrategy;

    /**
     * @var GetSingleStreamStrategyTableName
     */
    private $getSingleStreamStrategyTableName;

    public function __construct(
        GetSingleStreamStrategyTableName $getSingleStreamStrategyTableName,
        ?MessageConverter $messageConverter = null
    ) {
        $this->streamStrategy = new PersistenceStrategy\MariaDbSingleStreamStrategy(
            $messageConverter ?? new DefaultMessageConverter()
        );
        $this->getSingleStreamStrategyTableName = $getSingleStreamStrategyTableName;
    }

    /**
     * @return string[]
     */
    public function createSchema(string $tableName): array
    {
        return $this->streamStrategy->createSchema($tableName);
    }

    public function columnNames(): array
    {
        return $this->streamStrategy->columnNames();
    }

    public function prepareData(Iterator $streamEvents): array
    {
        return $this->streamStrategy->prepareData($streamEvents);
    }


    public function indexedMetadataFields(): array
    {
        return $this->streamStrategy->indexedMetadataFields();
    }

    public function generateTableName(StreamName $streamName): string
    {
        return $this->getSingleStreamStrategyTableName->execute($streamName);
    }

    public function indexName(): string
    {
        return $this->streamStrategy->indexName();
    }
}
