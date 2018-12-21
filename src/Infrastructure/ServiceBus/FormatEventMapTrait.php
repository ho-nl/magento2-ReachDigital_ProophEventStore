<?php
/**
 * Copyright © Reach Digital (https://www.reachdigital.io/)
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace ReachDigital\ProophEventStore\Infrastructure\ServiceBus;

trait FormatEventMapTrait
{
    /**
     * di.xml requires all arrays to have keys, but the CommandBus, QueryBus and EventBus require empty arrays.
     * @param array $eventMap
     * @return array
     */
    private function formatEventMap(array $eventMap): array
    {
        foreach ($eventMap as &$event) {
            /** @var array $event */
            foreach ($event as &$listener) {
                $listener = array_values($listener);
            }
        }
        return $eventMap;
    }
}
