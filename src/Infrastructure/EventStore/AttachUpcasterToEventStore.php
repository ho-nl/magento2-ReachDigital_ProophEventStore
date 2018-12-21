<?php
/**
 * Copyright © Reach Digital (https://www.reachdigital.io/)
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);
namespace ReachDigital\ProophEventStore\Infrastructure\EventStore;

use Prooph\EventStore\ActionEventEmitterEventStore;
use Prooph\EventStore\Plugin\UpcastingPlugin;
use Prooph\EventStore\Plugin\UpcastingPluginFactory;
use Prooph\EventStore\Upcasting\UpcasterChain;

class AttachUpcasterToEventStore
{
    /** @var UpcastingPlugin */
    private $plugin;

    public function __construct(
        UpcasterChain $upcasterChain,
        UpcastingPluginFactory $upcastingPluginFactory
    ) {
        $this->plugin = $upcastingPluginFactory->create(['upcaster' => $upcasterChain]);
    }

    public function attach(ActionEventEmitterEventStore $eventStore): void
    {
        $this->plugin->attachToEventStore($eventStore);
    }
}
