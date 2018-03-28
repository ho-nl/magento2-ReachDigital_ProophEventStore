<?php
/**
 * Copyright (c) Reach Digital (https://www.reachdigital.nl/)
 * See README.md for license details.
 */

declare(strict_types=1);

namespace ReachDigital\ProophEventStore\Infrastructure;

use Prooph\EventStore\EventStore;
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
        $streamName = new StreamName('event_stream_' . $streamNameStr);

        return $streamName;
    }
}
