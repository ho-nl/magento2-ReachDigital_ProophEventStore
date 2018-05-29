<?php
/**
 * Copyright (c) Reach Digital (https://www.reachdigital.nl/)
 * See README.md for license details.
 */
declare(strict_types=1);

namespace ReachDigital\ProophEventStore\Infrastructure;


use Prooph\Common\Event\ActionEventEmitter;

//@todo move to Infrastructure\Bus namespace
class QueryBus extends \Prooph\ServiceBus\QueryBus
{
    public function __construct(
        ActionEventEmitter $actionEventEmitter = null,
        QueryRouter $queryRouter
    ) {
        parent::__construct($actionEventEmitter);
        $queryRouter->attachToMessageBus($this);
    }
}
