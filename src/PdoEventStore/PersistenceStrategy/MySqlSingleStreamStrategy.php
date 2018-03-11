<?php
/**
 * Copyright (c) Reach Digital (https://www.reachdigital.nl/)
 * See README.md for license details.
 */

declare(strict_types=1);

namespace ReachDigital\ProophEventStore\PdoEventStore\PersistenceStrategy;

use Iterator;
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
final class MySqlSingleStreamStrategy implements PersistenceStrategy, HasQueryHint
{
    /**
     * @param string $tableName
     * @return string[]
     */
    public function createSchema(string $tableName): array
    {
        $statement = <<<EOT
CREATE TABLE `$tableName` (
    `no` BIGINT(20) NOT NULL AUTO_INCREMENT,
    `event_id` CHAR(36) COLLATE utf8_bin NOT NULL,
    `event_name` VARCHAR(100) COLLATE utf8_bin NOT NULL,
    `payload` JSON NOT NULL,
    `metadata` JSON NOT NULL,
    `created_at` DATETIME(6) NOT NULL,
    `aggregate_version` INT(11) UNSIGNED GENERATED ALWAYS AS (JSON_EXTRACT(metadata, '$._aggregate_version')) STORED NOT NULL,
    `aggregate_id` CHAR(36) CHARACTER SET utf8 COLLATE utf8_bin GENERATED ALWAYS AS (JSON_UNQUOTE(JSON_EXTRACT(metadata, '$._aggregate_id'))) STORED NOT NULL,
    `aggregate_type` VARCHAR(150) GENERATED ALWAYS AS (JSON_UNQUOTE(JSON_EXTRACT(metadata, '$._aggregate_type'))) STORED NOT NULL,
    PRIMARY KEY (`no`),
    UNIQUE KEY `ix_event_id` (`event_id`),
    UNIQUE KEY `ix_unique_event` (`aggregate_type`, `aggregate_id`, `aggregate_version`),
    KEY `ix_query_aggregate` (`aggregate_type`,`aggregate_id`,`no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
EOT;

        return [$statement];
    }

    public function columnNames(): array
    {
        return [
            'event_id',
            'event_name',
            'payload',
            'metadata',
            'created_at',
        ];
    }

    public function prepareData(Iterator $streamEvents): array
    {
        $data = [];

        foreach ($streamEvents as $event) {
            $data[] = $event->uuid()->toString();
            $data[] = $event->messageName();
            $data[] = json_encode($event->payload());
            $data[] = json_encode($event->metadata());
            $data[] = $event->createdAt()->format('Y-m-d\TH:i:s.u');
        }

        return $data;
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
        return 'ix_query_aggregate';
    }
}
