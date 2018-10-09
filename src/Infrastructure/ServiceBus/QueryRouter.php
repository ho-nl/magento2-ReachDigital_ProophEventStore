<?php
/**
 * Copyright © Reach Digital (https://www.reachdigital.io/)
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace ReachDigital\ProophEventStore\Infrastructure\ServiceBus;

class QueryRouter extends \Prooph\ServiceBus\Plugin\Router\QueryRouter
{
    use FormatEventMapTrait;

    public function __construct(array $messageMap = [])
    {
        parent::__construct($this->formatEventMap($messageMap));
    }
}
