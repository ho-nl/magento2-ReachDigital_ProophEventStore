<?php
declare(strict_types=1);

namespace ReachDigital\ProophEventStore\Infrastructure;

use Prooph\EventStore\Projection\ProjectionManager;
use Prooph\EventStore\Projection\Projector;
use Prooph\EventStore\Projection\ReadModel;
use Prooph\EventStore\Projection\ReadModelProjector;
use ReachDigital\ProophEventStore\Api\ProjectionInterface;
use ReachDigital\ProophEventStore\Test\Integration\Fixtures\Projection\UserReadModel;

//@todo move to Infrastructure\Projection namespace
class ProjectionContext
{
    /**
     * @var ProjectionManager
     */
    private $projectionManager;
    /**
     * @var ProjectionInterface
     */
    private $projection;
    /**
     * @var ReadModel
     */
    private $readModel;
    /**
     * @var string
     */
    private $name;
    /**
     * @var ReadModelProjector|Projector
     */
    private $projector;

    public function __construct(
        ProjectionManager $projectionManager,
        ProjectionInterface $projection,
        ReadModel $readModel = null,
        string $name
    ) {
        $this->projectionManager = $projectionManager;
        $this->projection = $projection;
        $this->readModel = $readModel;
        $this->name = $name;
    }

    /**
     * @return ReadModelProjector|Projector
     */
    public function projector(array $options = [])
    {
        if ($this->projector === null) {
            if ($this->readModel) {
                $this->projector = $this->projectionManager->createReadModelProjection(
                    $this->name,
                    $this->readModel,
                    $options
                );
            } else {
                $this->projector = $this->projectionManager->createProjection($this->name, $options);
            }
        }
        return $this->projector;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function readModel(): ReadModel
    {
        return $this->readModel;
    }

    public function projection(): ProjectionInterface
    {
        return $this->projection;
    }

    public function projectionManager(): ProjectionManager
    {
        return $this->projectionManager;
    }
}
