<?php

declare(strict_types=1);

namespace ReachDigital\ProophEventStore\Api;

use Prooph\EventStore\Projection\Projector;

interface ProjectionInterface
{
    public function project(Projector $projector): Projector;
}
