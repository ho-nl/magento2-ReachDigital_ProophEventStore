<?php
/**
 * Copyright (c) Reach Digital (https://www.reachdigital.nl/)
 * See README.md for license details.
 */

declare(strict_types=1);

namespace ReachDigital\ProophEventStore;

use Prooph\EventStore\EventStore;
use Prooph\EventStore\EventStoreFactory;
use ReachDigital\ProophEventStore\Infrastructure\ServiceBus\CommandBus;
use ReachDigital\ProophEventStore\Infrastructure\ServiceBus\CommandBusFactory;
use ReachDigital\ProophEventStore\Infrastructure\ServiceBus\EventBus;
use ReachDigital\ProophEventStore\Infrastructure\ServiceBus\EventBusFactory;
use ReachDigital\ProophEventStore\Infrastructure\ServiceBus\QueryBus;
use ReachDigital\ProophEventStore\Infrastructure\ServiceBus\QueryBusFactory;

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

    /**
     * @deprecated Use EventStore directly
     * @return EventStore
     */
    public function eventStore(): EventStore
    {
        return $this->eventStore;
    }

    /**
     * @deprecated Use CommandBus directly
     * @return CommandBus
     */
    public function commandBus(): CommandBus
    {
        return $this->commandBus;
    }

    /**
     * @deprecated Use EventBus directly
     * @return EventBus
     */
    public function eventBus(): EventBus
    {
        return $this->eventBus;
    }

    /**
     * @deprecated Use QueryBus directly
     * @return QueryBus
     */
    public function queryBus(): QueryBus
    {
        return $this->queryBus;
    }
}
