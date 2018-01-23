<?php


namespace ReachDigital\ProophEventStore\Infrastructure;


class EventRouter extends \Prooph\ServiceBus\Plugin\Router\EventRouter
{
    public function __construct(array $eventMap = null)
    {
        parent::__construct($this->formatEventMap($eventMap));
    }

    /**
     * @param array $eventMap
     * @return array
     */
    private function formatEventMap(array $eventMap): array
    {
        foreach ($eventMap as $eventName => &$event) {
            /** @var array $event */
            foreach ($event as &$listener) {
                $listener = array_values($listener);
            }
        }
        return $eventMap;
    }
}
