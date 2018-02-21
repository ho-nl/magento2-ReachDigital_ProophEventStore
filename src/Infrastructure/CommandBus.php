<?php
declare(strict_types=1);

namespace ReachDigital\ProophEventStore\Infrastructure;

use Prooph\Common\Event\ActionEventEmitter;

class CommandBus extends \Prooph\ServiceBus\CommandBus
{
    public function __construct(
        ActionEventEmitter $actionEventEmitter = null,
        CommandRouter $commandRouter
    ) {
        parent::__construct($actionEventEmitter);
        $commandRouter->attachToMessageBus($this);
    }
}
