<?php
declare(strict_types=1);

namespace ReachDigital\ProophEventStore\Test\Integration\Fixtures\Infrastructure;

use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use Prooph\EventStore\Pdo\Projection\MySqlProjectionManager;
use Prooph\EventStore\Projection\ReadModelProjector;
use ReachDigital\ProophEventStore\Api\ProjectionInterface;
use ReachDigital\ProophEventStore\Infrastructure\ProjectionContext;
use ReachDigital\ProophEventStore\Infrastructure\ProjectionContextPool;
use ReachDigital\ProophEventStore\Test\Integration\Fixtures\Projection\UserProjection;
use ReachDigital\ProophEventStore\Test\Integration\Fixtures\Projection\UserReadModel;

class ProjectionPoolTest extends TestCase
{
    /** @var  \Magento\Framework\ObjectManagerInterface */
    private $objectManager;

    protected function setUp()
    {
        $this->objectManager = Bootstrap::getObjectManager();
    }

    /**
     * @test
     */
    public function projection_pool_accepts_projection_context_objects()
    {
        /** @var ProjectionContextPool $projectionPool */
        $projectionPool = $this->objectManager->create(ProjectionContextPool::class);

        $this->assertInstanceOf(UserReadModel::class, $this->objectManager->create(UserReadModel::class));

        /** @var ProjectionContext $userProjectionContext */
        $userProjectionContext = $this->objectManager->create(ProjectionContext::class, [
            'projectionManager' => $this->objectManager->get(MySqlProjectionManager::class),
            'projection' => $this->objectManager->create(UserProjection::class),
            'readModel' => $this->objectManager->get(UserReadModel::class),
            'name' => 'user_projection',
        ]);
        $projectionPool->add($userProjectionContext);

        $this->assertInstanceOf(ReadModelProjector::class, $userProjectionContext->projector());
        $this->assertNotCount(0, $projectionPool->all());

        foreach ($projectionPool->all() as $projectionContext) {
            $this->assertInstanceOf(ProjectionInterface::class, $projectionContext->projection());
        }
    }

    /**
     * @test
     */
    public function projection_pool_can_be_initiated_with_arguments()
    {
        /** @var ProjectionContextPool $projectionPool */
        $projectionPool = $this->objectManager->create(ProjectionContextPool::class, [
            'projectionContexts' => [
                'user_projection' => [
                    'projectionManager' => $this->objectManager->get(MySqlProjectionManager::class),
                    'projection' => $this->objectManager->create(UserProjection::class),
                    'readModel' => $this->objectManager->get(UserReadModel::class),
                ],
            ],
        ]);

        $this->assertNotCount(0, $projectionPool->all());

        foreach ($projectionPool->all() as $projectionContext) {
            $this->assertInstanceOf(ProjectionInterface::class, $projectionContext->projection());
        }
    }
}
