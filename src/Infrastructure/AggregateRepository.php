<?php
/**
 * Copyright © Reach Digital (https://www.reachdigital.io/)
 * See LICENSE.txt for license details.
 */
namespace ReachDigital\ProophEventStore\Infrastructure;

use Prooph\EventSourcing\Aggregate\AggregateTranslator;
use Prooph\EventSourcing\Aggregate\AggregateType;
use Prooph\EventStore\EventStore;
use Prooph\EventStore\StreamName;
use Prooph\SnapshotStore\SnapshotStore;

abstract class AggregateRepository extends \Prooph\EventSourcing\Aggregate\AggregateRepository
{
    public function __construct(
        EventStore $eventStore,
        AggregateTranslator $aggregateTranslator,
        SnapshotStore $snapshotStore = null,
        string $streamName,
        string $aggregateRoot,
        bool $oneStreamPerAggregate = false,
        bool $disableIdentityMap = false,
        array $metadata = []
    ) {
        parent::__construct(
            $eventStore,
            AggregateType::fromAggregateRootClass($aggregateRoot),
            $aggregateTranslator,
            $snapshotStore,
            new StreamName($streamName),
            $oneStreamPerAggregate,
            $disableIdentityMap,
            $metadata
        );
    }
}
