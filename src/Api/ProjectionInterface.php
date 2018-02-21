<?php

declare(strict_types=1);

namespace ReachDigital\ProophEventStore\Api;

use Prooph\EventStore\Projection\Projector;
use Prooph\EventStore\Projection\ReadModelProjector;

interface ProjectionInterface
{
    /**
     * @param ReadModelProjector|Projector $projector
     * @return ReadModelProjector|Projector
     */
    public function project($projector);
}
