<?php
/**
 * Copyright © Reach Digital (https://www.reachdigital.io/)
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace ReachDigital\ProophEventStore\Infrastructure\Projection;

use ReachDigital\ProophEventStore\Exception\ProjectionNotFoundException;

class ProjectionContextPool
{

    /** @var ProjectionContext[] */
    private $projectionContexts = [];

    /**
     * ProjectionManagerPool constructor.
     * @param ProjectionContext[] $projectionContexts
     */
    public function __construct(
        ProjectionContextFactory $projectionContextFactory,
        array $projectionContexts = []
    ) {
        foreach ($projectionContexts as $name => $projectionContext) {
            $this->add(
                $projectionContextFactory->create(['name' => $name] + $projectionContext)
            );
        }
    }

    public function add(ProjectionContext $context): void
    {
        $this->projectionContexts[$context->name()] = $context;
    }

    /**
     * @param string $name
     * @return ProjectionContext
     * @throws ProjectionNotFoundException
     */
    public function get(string $name)
    {
        if (! isset($this->projectionContexts[$name])) {
            throw ProjectionNotFoundException::withName($name);
        }
        return $this->projectionContexts[$name];
    }

    /**
     * @param string $name
     * @throws ProjectionNotFoundException
     */
    public function remove(string $name): void
    {
        if (! isset($this->projectionContexts[$name])) {
            throw ProjectionNotFoundException::withName($name);
        }
        unset($this->projectionContexts[$name]);
    }

    /**
     * @return ProjectionContext[]
     */
    public function all(): array
    {
        return $this->projectionContexts;
    }
}
