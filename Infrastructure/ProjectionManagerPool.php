<?php


namespace ReachDigital\ProophEventStore\Infrastructure;


use Prooph\EventStore\Projection\ProjectionManager;
use ReachDigital\ProophEventStore\Exception\ProjectionManagerNotFoundException;

class ProjectionManagerPool
{

    /** @var ProjectionManager[] */
    private $projectionManagers;

    public function __construct(array $projectionManagers = [])
    {
        foreach ($projectionManagers as $name => $manager) {
            $this->add($manager, $name);
        }
    }

    public function add(ProjectionManager $manager, string $name): void
    {
        $this->projectionManagers[$name] = $manager;
    }

    public function get(string $name): ProjectionManager
    {
        if (! isset($this->projectionManagers[$name])) {
            throw new ProjectionManagerNotFoundException(__('Projection not found %1', $name));
        }
        return $this->projectionManagers[$name];
    }

    public function remove(string $name): void {
        if (! isset($this->projectionManagers[$name])) {
            throw new ProjectionManagerNotFoundException(__('Projection not found %1', $name));
        }
        unset($this->projectionManagers[$name]);
    }

    public function all(): array {
        return $this->projectionManagers;
    }
}
