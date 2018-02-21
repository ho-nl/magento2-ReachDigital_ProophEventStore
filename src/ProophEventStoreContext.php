<?php
/**
 * Copyright (c) Reach Digital (https://www.reachdigital.nl/)
 * See README.md for license details.
 */

declare(strict_types=1);

namespace ReachDigital\ProophEventStore;

use Prooph\EventStore\EventStore;
use Prooph\EventStore\EventStoreFactory;
use ReachDigital\ProophEventStore\Infrastructure\CommandBus;
use ReachDigital\ProophEventStore\Infrastructure\CommandBusFactory;
use ReachDigital\ProophEventStore\Infrastructure\EventBus;
use ReachDigital\ProophEventStore\Infrastructure\EventBusFactory;
use ReachDigital\ProophEventStore\Infrastructure\QueryBus;
use ReachDigital\ProophEventStore\Infrastructure\QueryBusFactory;

class ProophEventStoreContext
{
    /** @var CommandBus */
    private $commandBus;

    /** @var EventBus */
    private $eventBus;

    /** @var QueryBus */
    private $queryBus;

    /** @var EventStore */
    private $eventStore;

    public function __construct(
        CommandBusFactory $commandBusFactory,
        EventBusFactory $eventBusFactory,
        QueryBusFactory $queryBusFactory,
        EventStoreFactory $eventStoreFactory
    ) {
        $this->commandBus = $commandBusFactory->create();
        $this->eventBus = $eventBusFactory->create();
        $this->queryBus = $queryBusFactory->create();
        $this->eventStore = $eventStoreFactory->create();
    }

    public function eventStore(): EventStore
    {
        return $this->eventStore;
    }

    public function commandBus(): CommandBus
    {
        return $this->commandBus;
    }

    public function eventBus(): EventBus
    {
        return $this->eventBus;
    }

    public function queryBus(): QueryBus
    {
        return $this->queryBus;
    }
}
