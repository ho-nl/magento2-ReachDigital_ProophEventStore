<?php
declare(strict_types=1);

namespace ReachDigital\ProophEventStore\Test\Integration\Fixtures\Model\Event;

use Prooph\EventSourcing\AggregateChanged;

class UserRegistered extends AggregateChanged
{
    public function email(): string
    {
        return $this->payload['email'];
    }

    public function password(): string
    {
        return $this->payload['password'];
    }
}
