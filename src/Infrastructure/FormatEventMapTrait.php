<?php
declare(strict_types=1);
/**
 * Copyright (c) Reach Digital (https://www.reachdigital.nl/)
 * See README.md for license details.
 */

namespace ReachDigital\ProophEventStore\Infrastructure;

//@todo move to Infrastructure\Bus namespace
trait FormatEventMapTrait
{
    /**
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
