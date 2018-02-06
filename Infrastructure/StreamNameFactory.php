<?php
/**
 * Copyright (c) Reach Digital (https://www.reachdigital.nl/)
 * See README.md for license details.
 */

namespace ReachDigital\ProophEventStore\Infrastructure;


use Prooph\EventStore\EventStore;
use Prooph\EventStore\Stream;
use Prooph\EventStore\StreamName;

class StreamNameFactory
{
    /**
     * @var EventStore
     */
    private $eventStore;

    public function __construct(
        EventStore $eventStore
    ) {
        $this->eventStore = $eventStore;
    }

    public function create(string $streamNameStr): StreamName
    {
        $streamName = new StreamName($streamNameStr);
        if (! $this->eventStore->hasStream($streamName)) {
            $this->eventStore->create(new Stream($streamName, new \ArrayIterator()));
        }
        return $streamName;
    }
}
