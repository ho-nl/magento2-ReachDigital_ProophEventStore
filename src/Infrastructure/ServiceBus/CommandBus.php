<?php
/**
 * Copyright © Reach Digital (https://www.reachdigital.io/)
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace ReachDigital\ProophEventStore\Infrastructure\ServiceBus;

use Magento\Framework\Event\Manager as EventManager;
use Prooph\Common\Event\ActionEventEmitter;

/**
 * Class CommandBus
 * @todo Remove EventManager in Magento 2.3, all listeners can be replaced by plugins
 * @package ReachDigital\ProophEventStore\Infrastructure
 */
class CommandBus extends \Prooph\ServiceBus\CommandBus
{
    /** @var CommandRouter */
    private $commandRouter;

    /**
     * @var EventManager
     */
    private $eventManager;

    public function __construct(
        ActionEventEmitter $actionEventEmitter = null,
        CommandRouter $commandRouter,
        EventManager $eventManager
    ) {
        parent::__construct($actionEventEmitter);
        $this->commandRouter = $commandRouter;
        $this->eventManager = $eventManager;
        $commandRouter->attachToMessageBus($this);
    }

    public function commandRouter(): CommandRouter
    {
        return $this->commandRouter;
    }

    /**
     * Extended to dispatch Magento events
     *
     * @param mixed $command
     */
    public function dispatch($command): void
    {
        $this->eventManager->dispatch('prooph_command_bus_dispatch_before', ['command' => $command]);

        parent::dispatch($command);

        $this->eventManager->dispatch('prooph_command_bus_dispatch_after', ['command' => $command]);
    }
}
