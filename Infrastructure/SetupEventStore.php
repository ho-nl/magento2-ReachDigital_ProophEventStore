<?php


namespace ReachDigital\ProophEventStore\Infrastructure;


use Prooph\EventStore\ActionEventEmitterEventStore;
use Prooph\EventStore\EventStore;
use Prooph\EventStoreBusBridge\EventPublisher;

class SetupEventStore
{
    /**
     * @var EventPublisher
     */
    private $eventPublisher;
    /**
     * @var EventStore
     */
    private $eventStore;

    public function __construct(
        EventPublisher $eventPublisher,
        ActionEventEmitterEventStore $eventStore
    ) {
        $this->eventPublisher = $eventPublisher;
        $this->eventStore = $eventStore;
    }


    public function setup(): void
    {
        $this->eventPublisher->attachToEventStore($this->eventStore);
    }
}
