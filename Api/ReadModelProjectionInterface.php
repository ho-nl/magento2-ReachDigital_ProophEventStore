<?php

declare(strict_types=1);

namespace ReachDigital\ProophEventStore\Api;

use Prooph\EventStore\Projection\ReadModelProjector;

interface ReadModelProjectionInterface
{
    public function project(ReadModelProjector $projector): ReadModelProjector;
}
