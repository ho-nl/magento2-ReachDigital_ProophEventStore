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
use Prooph\EventStore\Pdo\PersistenceStrategy;
use Prooph\EventStore\StreamName;
use Prooph\EventStore\Util\Assertion;

/**
 * Class MySqlSingleStreamStrategy
 * @see \Prooph\EventStore\Pdo\PersistenceStrategy\MySqlSingleStreamStrategy
 *  - Implements a more sane table name, instead of the has currently being used.
 *
 * @package ReachDigital\ProophEventStore\PdoEventStore\PersistenceStrategy
 */
final class MysqlSingleStreamStrategyProxy implements PersistenceStrategy, HasQueryHint
{
    /**
     * @var PersistenceStrategy\MySqlSingleStreamStrategy
     */
    private $streamStrategy;

    public function __construct(
        ?MessageConverter $messageConverter = null
    ) {
        $this->streamStrategy = new PersistenceStrategy\MySqlSingleStreamStrategy(
            $messageConverter ?? new DefaultMessageConverter()
        );
    }

    /**
     * @param string $tableName
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


    /**
     * This class is modified
     * @param StreamName $streamName
     * @return string
     * @throws \Assert\AssertionFailedException
     */
    public function generateTableName(StreamName $streamName): string
    {
        $tableName = $streamName->toString();
        Assertion::regex($tableName, '/^[a-zA-Z0-9_]+$/');
        Assertion::maxLength($tableName, 64);
        return $tableName;
    }

    public function indexName(): string
    {
        return $this->streamStrategy->indexName();
    }
}
