<?php
declare(strict_types=1);
/**
 * Copyright (c) Reach Digital (https://www.reachdigital.nl/)
 * See README.md for license details.
 */

namespace ReachDigital\ProophEventStore\Infrastructure;


class QueryRouter extends \Prooph\ServiceBus\Plugin\Router\QueryRouter
{
    use FormatEventMapTrait;

    public function __construct(array $messageMap = [])
    {
        parent::__construct($this->formatEventMap($messageMap));
    }
}
