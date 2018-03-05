<?php
declare(strict_types=1);

namespace ReachDigital\ProophEventStore\Infrastructure;

use Prooph\Common\Event\ActionEventEmitter;

class CommandBus extends \Prooph\ServiceBus\CommandBus
{
    /** @var CommandRouter */
    private $commandRouter;

    public function __construct(
        ActionEventEmitter $actionEventEmitter = null,
        CommandRouter $commandRouter
    ) {
        parent::__construct($actionEventEmitter);
        $this->commandRouter = $commandRouter;
        $commandRouter->attachToMessageBus($this);
    }

    public function commandRouter()
    {
        return $this->commandRouter;
    }
}
