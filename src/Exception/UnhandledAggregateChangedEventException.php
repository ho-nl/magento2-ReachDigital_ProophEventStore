<?php
/**
 * Copyright (c) Reach Digital (https://www.reachdigital.nl/)
 * See README.md for license details.
 */

declare(strict_types=1);
namespace ReachDigital\ProophEventStore\Exception;


use Magento\Framework\Exception\LocalizedException;
use Prooph\EventSourcing\AggregateChanged;
use Prooph\EventSourcing\AggregateRoot;

class UnhandledAggregateChangedEventException extends LocalizedException
{
    public static function withEvent(
        AggregateRoot $aggregateRoot,
        AggregateChanged $aggregateChanged
    ) : UnhandledAggregateChangedEventException {
        return new self(__(
            'Missing event handler method %1 for aggregate root %2',
            \get_class($aggregateChanged),
            \get_class($aggregateRoot)
        ));
    }
}
