<?php


namespace ReachDigital\ProophEventStore\Test\Integration\Console\Command;


use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use PHPUnit\Framework\TestCase;
use Prooph\EventStore\Pdo\Projection\MySqlProjectionManager;
use ReachDigital\ProophEventStore\Console\Command\ProjectionRunCommand;
use ReachDigital\ProophEventStore\Console\Command\ProjectionStateCommand;
use ReachDigital\ProophEventStore\Infrastructure\ProjectionContextPool;
use ReachDigital\ProophEventStore\Test\Integration\Fixtures\Projection\UserProjection;
use ReachDigital\ProophEventStore\Test\Integration\Fixtures\Projection\UserReadModel;
use Symfony\Component\Console\Tester\CommandTester;

class ProjectionRunCommandTest extends TestCase
{
    /** @var ObjectManager */
    private $objectManager;

    /** @var ProjectionStateCommand */
    private $command;

    /** @var CommandTester */
    private $tester;

    protected function setUp() {
        $this->objectManager = Bootstrap::getObjectManager();

        $this->command = $this->objectManager->create(ProjectionRunCommand::class, [
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
     * @test
     */
    public function should_run_user_projection_once()
    {
        $this->tester->execute([
            'projection-name' => 'user_projection',
            '--run-once' => true
        ]);
        $output = $this->tester->getDisplay();
        echo $output;
    }
}
