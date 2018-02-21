<?php
declare(strict_types=1);


namespace ReachDigital\ProophEventStore\Infrastructure;


use ReachDigital\ProophEventStore\Api\ProjectionInterface;
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
            throw new ProjectionNotFoundException(__('Projection not found %1', $name));
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
            throw new ProjectionNotFoundException(__('Projection not found %1', $name));
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
