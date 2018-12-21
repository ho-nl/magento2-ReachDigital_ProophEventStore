<?php
/**
 * Copyright (c) Reach Digital (https://www.reachdigital.nl/)
 * See README.md for license details.
 */
declare(strict_types=1);

namespace ReachDigital\ProophEventStore\Infrastructure\ServiceBus;

use Prooph\Common\Event\ActionEventEmitter;

class QueryBus extends \Prooph\ServiceBus\QueryBus
{
    public function __construct(
        QueryRouter $queryRouter,
        ActionEventEmitter $actionEventEmitter = null
    ) {
        parent::__construct($actionEventEmitter);
        $queryRouter->attachToMessageBus($this);
    }
}
