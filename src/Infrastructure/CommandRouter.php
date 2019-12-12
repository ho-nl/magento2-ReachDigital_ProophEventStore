<?php
/**
 * Copyright (c) Reach Digital (https://www.reachdigital.nl/)
 * See README.md for license details.
 */

declare(strict_types=1);
namespace ReachDigital\ProophEventStore\Infrastructure;

//@todo move to Infrastructure\Bus namespace
class CommandRouter extends \Prooph\ServiceBus\Plugin\Router\CommandRouter
{
    use FormatEventMapTrait;

    public function __construct(array $messageMap = [])
    {
        parent::__construct($this->formatEventMap($messageMap));
    }
}
