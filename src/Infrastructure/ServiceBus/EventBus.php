<?php
/**
 * Copyright © Reach Digital (https://www.reachdigital.io/)
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace ReachDigital\ProophEventStore\Infrastructure\ServiceBus;

use Prooph\Common\Event\ActionEventEmitter;
use Prooph\EventStore\ActionEventEmitterEventStoreFactory;
use Prooph\EventStoreBusBridge\EventPublisher;
use Prooph\EventStoreBusBridge\EventPublisherFactory;
use Prooph\ServiceBus\Plugin\Router\EventRouter;
use ReachDigital\ProophEventStore\Infrastructure\EventStore\AttachUpcasterToEventStore;

class EventBus extends \Prooph\ServiceBus\EventBus
{
    public function __construct(
        ActionEventEmitter $actionEventEmitter,
        EventRouter $eventRouter,
        EventPublisherFactory $eventPublisherFactory,
        ActionEventEmitterEventStoreFactory $actionEventEmitterEventStoreFactory,
        AttachUpcasterToEventStore $attachUpcasterToEventStore
    ) {
        parent::__construct($actionEventEmitter);
        $eventRouter->attachToMessageBus($this);

        /** @var EventPublisher $eventPublisher */
        $eventPublisher = $eventPublisherFactory->create([
            'eventBus' => $this
        ]);
        $actionEventEmitterEventStore = $actionEventEmitterEventStoreFactory->create([
            'actionEventEmitter' => $actionEventEmitter
        ]);
        $eventPublisher->attachToEventStore($actionEventEmitterEventStore);

        $attachUpcasterToEventStore->attach($actionEventEmitterEventStore);

        $this->construct();
    }

    public function construct(): void
    {
        //Magento plugin hook
    }
}
