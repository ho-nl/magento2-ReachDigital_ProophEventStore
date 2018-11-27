<?php
/**
 * Copyright © Reach Digital (https://www.reachdigital.io/)
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);
namespace ReachDigital\ProophEventStore\Infrastructure\EventStore;

use Iterator;
use Prooph\EventStore\EventStore;
use Prooph\EventStore\Metadata\MetadataMatcher;
use Prooph\EventStore\Pdo\MariaDbEventStoreFactory;
use Prooph\EventStore\Pdo\MySqlEventStoreFactory;
use Prooph\EventStore\Plugin\UpcastingPlugin;
use Prooph\EventStore\Stream;
use Prooph\EventStore\StreamName;
use Prooph\EventStore\Upcasting\UpcasterChain;
use ReachDigital\ProophEventStore\Infrastructure\Pdo\DbType;
use ReachDigital\ProophEventStore\Infrastructure\Pdo\DbTypeResolver;

class EventStoreProxy implements EventStore
{
    /** @var EventStore */
    private $eventStore;

    public function __construct(
        MysqlEventStoreFactory $mySqlEventStoreFactory,
        MysqlSingleStreamStrategyFactory $mysqlSingleStreamStrategyFactory,
        MariaDbEventStoreFactory $mariaDbEventStoreFactory,
        MariaDbSingleStreamStrategyFactory $mariaDbSingleStreamStrategy,
        DbTypeResolver $dbTypeResolver
    ) {
        if ($dbTypeResolver->get()->equals(DbType::mySql())) {
            $this->eventStore = $mySqlEventStoreFactory->create([
                'persistenceStrategy' => $mysqlSingleStreamStrategyFactory->create()
            ]);
        } else {
            $this->eventStore = $mariaDbEventStoreFactory->create([
                'persistenceStrategy' => $mariaDbSingleStreamStrategy->create()
            ]);
        }

//        (new UpcastingPlugin($upcasterChain))->attachToEventStore($this->eventStore);
    }

    public function instance(): EventStore
    {
        return $this->eventStore;
    }

    public function eventStorePlugin(EventStore $eventStore): void
    {
        //Add upcasting plugin http://docs.getprooph.org/event-store/upcasting.html
    }

    public function updateStreamMetadata(StreamName $streamName, array $newMetadata): void
    {
        $this->eventStore->updateStreamMetadata($streamName, $newMetadata);
    }

    public function create(Stream $stream): void
    {
        $this->eventStore->create($stream);
    }

    public function appendTo(StreamName $streamName, Iterator $streamEvents): void
    {
        $this->eventStore->appendTo($streamName, $streamEvents);
    }

    public function delete(StreamName $streamName): void
    {
        $this->eventStore->delete($streamName);
    }

    public function fetchStreamMetadata(StreamName $streamName): array
    {
        return $this->eventStore->fetchStreamMetadata($streamName);
    }

    public function hasStream(StreamName $streamName): bool
    {
        return $this->eventStore->hasStream($streamName);
    }

    public function load(
        StreamName $streamName,
        int $fromNumber = 1,
        int $count = null,
        MetadataMatcher $metadataMatcher = null
    ): Iterator {
        return $this->eventStore->load($streamName, $fromNumber, $count, $metadataMatcher);
    }

    public function loadReverse(
        StreamName $streamName,
        int $fromNumber = null,
        int $count = null,
        MetadataMatcher $metadataMatcher = null
    ): Iterator {
        return $this->eventStore->loadReverse($streamName, $fromNumber, $count, $metadataMatcher);
    }

    /**
     * @return StreamName[]
     */
    public function fetchStreamNames(
        ?string $filter,
        ?MetadataMatcher $metadataMatcher,
        int $limit = 20,
        int $offset = 0
    ): array {
        return $this->eventStore->fetchStreamNames($filter, $metadataMatcher, $limit, $offset);
    }

    /**
     * @return StreamName[]
     */
    public function fetchStreamNamesRegex(
        string $filter,
        ?MetadataMatcher $metadataMatcher,
        int $limit = 20,
        int $offset = 0
    ): array {
        return $this->eventStore->fetchStreamNamesRegex($filter, $metadataMatcher, $limit, $offset);
    }

    /**
     * @return string[]
     */
    public function fetchCategoryNames(?string $filter, int $limit = 20, int $offset = 0): array
    {
        return $this->eventStore->fetchCategoryNames($filter, $limit, $offset);
    }

    /**
     * @return string[]
     */
    public function fetchCategoryNamesRegex(string $filter, int $limit = 20, int $offset = 0): array
    {
        return $this->eventStore->fetchCategoryNamesRegex($filter, $limit, $offset);
    }
}