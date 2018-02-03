<?php


namespace ReachDigital\ProophEventStore\Test\Integration\Console\Command;


use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use PHPUnit\Framework\TestCase;
use Prooph\EventStore\Pdo\Projection\MySqlProjectionManager;
use ReachDigital\ProophEventStore\Console\Command\ProjectionDeleteCommand;
use ReachDigital\ProophEventStore\Console\Command\ProjectionRunCommand;
use ReachDigital\ProophEventStore\Console\Command\ProjectionStopCommand;
use ReachDigital\ProophEventStore\Infrastructure\ProjectionContextPool;
use ReachDigital\ProophEventStore\Test\Integration\Fixtures\Projection\UserProjection;
use ReachDigital\ProophEventStore\Test\Integration\Fixtures\Projection\UserReadModel;
use Symfony\Component\Console\Tester\CommandTester;

class ProjectionDeleteCommandTest extends TestCase
{
    /** @var ObjectManager */
    private $objectManager;

    /** @var CommandTester */
    private $tester;

    protected function setUp() {
        $this->objectManager = Bootstrap::getObjectManager();

        $args = [
            'projectionContextPool' => $this->objectManager->create(ProjectionContextPool::class, [
                'projectionContexts' => [
                    'user_projection' => [
                        'projectionManager' => $this->objectManager->get(MySqlProjectionManager::class),
                        'projection' => $this->objectManager->create(UserProjection::class),
                        'readModel' => $this->objectManager->get(UserReadModel::class),
                    ]
                ]
            ])
        ];

        $runCommand = $this->objectManager->create(ProjectionRunCommand::class, $args);
        (new CommandTester($runCommand))->execute([
            'projection-name' => 'user_projection',
            '--run-once' => true
        ]);

        $command = $this->objectManager->create(ProjectionDeleteCommand::class, $args);
        $this->tester = new CommandTester($command);

    }

    /**
     * @test
     */
    public function should_delete_projection()
    {
        $this->tester->execute([
            'projection-name' => 'user_projection',
            '--with-emitted-events' => true
        ]);
        $output = $this->tester->getDisplay();
        echo $output;
    }
}
