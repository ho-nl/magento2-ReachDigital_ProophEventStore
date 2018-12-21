<?php
/**
 * Copyright © Reach Digital (https://www.reachdigital.io/)
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace ReachDigital\ProophEventStore\Infrastructure\Projection;

use Prooph\EventStore\Exception\ProjectionNotFound;
use Prooph\EventStore\Pdo\Projection\MariaDbProjectionManagerFactory;
use Prooph\EventStore\Pdo\Projection\MySqlProjectionManagerFactory;
use Prooph\EventStore\Projection\ProjectionManager;
use Prooph\EventStore\Projection\ProjectionStatus;
use Prooph\EventStore\Projection\Projector;
use Prooph\EventStore\Projection\Query;
use Prooph\EventStore\Projection\ReadModel;
use Prooph\EventStore\Projection\ReadModelProjector;
use ReachDigital\ProophEventStore\Infrastructure\EventStore\EventStoreProxy;
use ReachDigital\ProophEventStore\Infrastructure\Pdo\DbType;
use ReachDigital\ProophEventStore\Infrastructure\Pdo\DbTypeResolver;

class ProjectionManagerProxy implements ProjectionManager
{

    /** @var ProjectionManager */
    private $projectionManager;

    public function __construct(
        MySqlProjectionManagerFactory $mySqlProjectionManagerFactory,
        MariaDbProjectionManagerFactory $mariaDbProjectionManagerFactory,
        EventStoreProxy $eventStoreProxy,
        DbTypeResolver $dbTypeResolver
    ) {
        if ($dbTypeResolver->get()->equals(DbType::mySql())) {
            $this->projectionManager = $mySqlProjectionManagerFactory->create([
                'eventStore' => $eventStoreProxy->instance()
            ]);
        } else {
            $this->projectionManager = $mariaDbProjectionManagerFactory->create([
                'eventStore' => $eventStoreProxy->instance()
            ]);
        }
    }

    public function createQuery(): Query
    {
        return $this->projectionManager->createQuery();
    }

    public function createProjection(
        string $name,
        array $options = []
    ): Projector {
        return $this->projectionManager->createProjection($name, $options);
    }

    public function createReadModelProjection(
        string $name,
        ReadModel $readModel,
        array $options = []
    ): ReadModelProjector {
        return $this->projectionManager->createReadModelProjection($name, $readModel, $options);
    }

    /**
     * @throws ProjectionNotFound
     */
    public function deleteProjection(string $name, bool $deleteEmittedEvents): void
    {
        $this->projectionManager->deleteProjection($name, $deleteEmittedEvents);
    }

    /**
     * @throws ProjectionNotFound
     */
    public function resetProjection(string $name): void
    {
        $this->projectionManager->resetProjection($name);
    }

    /**
     * @throws ProjectionNotFound
     */
    public function stopProjection(string $name): void
    {
        $this->projectionManager->stopProjection($name);
    }

    /**
     * @return string[]
     */
    public function fetchProjectionNames(?string $filter, int $limit = 20, int $offset = 0): array
    {
        return $this->projectionManager->fetchProjectionNames($filter, $limit, $offset);
    }

    /**
     * @return string[]
     */
    public function fetchProjectionNamesRegex(string $regex, int $limit = 20, int $offset = 0): array
    {
        return $this->projectionManager->fetchProjectionNamesRegex($regex, $limit, $offset);
    }

    /**
     * @throws ProjectionNotFound
     */
    public function fetchProjectionStatus(string $name): ProjectionStatus
    {
        return $this->projectionManager->fetchProjectionStatus($name);
    }

    /**
     * @throws ProjectionNotFound
     */
    public function fetchProjectionStreamPositions(string $name): array
    {
        return $this->projectionManager->fetchProjectionStreamPositions($name);
    }

    /**
     * @throws ProjectionNotFound
     */
    public function fetchProjectionState(string $name): array
    {
        return $this->projectionManager->fetchProjectionState($name);
    }
}
