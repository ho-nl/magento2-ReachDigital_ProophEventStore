<?php
declare(strict_types=1);


namespace ReachDigital\ProophEventStore\Test\Integration\Console\Command;


use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use PHPUnit\Framework\TestCase;
use Prooph\EventStore\Pdo\Projection\MySqlProjectionManager;
use ReachDigital\ProophEventStore\Console\Command\ProjectionNamesCommand;
use ReachDigital\ProophEventStore\Infrastructure\Projection\ProjectionContextPool;
use ReachDigital\ProophEventStore\Test\Integration\Fixtures\Projection\UserProjection;
use ReachDigital\ProophEventStore\Test\Integration\Fixtures\Projection\UserReadModel;
use Symfony\Component\Console\Tester\CommandTester;

class ProjectionNamesCommandTest extends TestCase
{
    /** @var ObjectManager */
    private $objectManager;
    
    /** @var ProjectionNamesCommand */
    private $command;
    
    /** @var CommandTester */
    private $tester;

    protected function setUp() {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->command = $this->objectManager->create(ProjectionNamesCommand::class, [
            'projectionContextPool' => $this->objectManager->create(ProjectionContextPool::class, [
                'projectionContexts' => [
                    'user_projection' => [
                        'projectionManager' => $this->objectManager->get(MySqlProjectionManager::class),
                        'projection' => $this->objectManager->create(UserProjection::class),
                        'readModel' => $this->objectManager->get(UserReadModel::class),
                    ]
                ]
            ])
        ]);
        $this->tester = new CommandTester($this->command);
        
    }
    
    /**
     * @test get projection names
     */
    public function can_get_projection_names()
    {
        $this->tester->execute([]);
        $output = $this->tester->getDisplay();

        $this->assertEquals(
<<<OUTPUT
Projection names
+-----------------+-----------------------------------------------------------------------------------+------------------------------------------------------------------+
| name            | projection                                                                        | projector                                                        |
+-----------------+-----------------------------------------------------------------------------------+------------------------------------------------------------------+
| user_projection | ReachDigital\ProophEventStore\Test\Integration\Fixtures\Projection\UserProjection | Prooph\EventStore\Pdo\Projection\PdoEventStoreReadModelProjector |
+-----------------+-----------------------------------------------------------------------------------+------------------------------------------------------------------+

OUTPUT

, $output);
    }
}
