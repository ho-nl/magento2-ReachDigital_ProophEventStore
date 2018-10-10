<?php
declare(strict_types=1);


namespace ReachDigital\ProophEventStore\Test\Integration\Command;

use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use PHPUnit\Framework\TestCase;
use Prooph\ServiceBus\CommandBus;
use Prooph\ServiceBus\Plugin\Router\CommandRouter;
use Ramsey\Uuid\Uuid;
use ReachDigital\ProophEventStore\Infrastructure\ServiceBus\EventBus;
use ReachDigital\ProophEventStore\ProophEventStoreContext;
use ReachDigital\ProophEventStore\Test\Integration\Fixtures\Infrastructure\UserRepository;
use ReachDigital\ProophEventStore\Test\Integration\Fixtures\Model\Command\ChangeEmail;
use ReachDigital\ProophEventStore\Test\Integration\Fixtures\Model\Command\RegisterUser;
use ReachDigital\ProophEventStore\Test\Integration\Fixtures\Model\Handler\ChangeEmailHandler;
use ReachDigital\ProophEventStore\Test\Integration\Fixtures\Model\Handler\RegisterUserHandler;
use ReachDigital\ProophEventStore\Test\Integration\Fixtures\User;

class RegisterUserTest extends TestCase
{
    /** @var ObjectManager */
    private $objectManager;

    protected function setUp()
    {
        $this->objectManager = Bootstrap::getObjectManager();
    }


    /**
     * @test
     */
    public function command_and_event_bus_are_correctly_instantiated()
    {
        /** @var ProophEventStoreContext $es */
        $es = $this->objectManager->get(ProophEventStoreContext::class);

        $commandBus = $es->commandBus();
        $this->assertInstanceOf(CommandBus::class, $commandBus);

        $commandBusEventsRefl = new \ReflectionProperty($commandBus, 'events');
        $commandBusEventsRefl->setAccessible(true);
        $commandBusEvents = $commandBusEventsRefl->getValue($commandBus);


        $eventBus = $this->objectManager->get(EventBus::class);
        $this->assertInstanceOf(EventBus::class, $eventBus);


        $eventBusEventsRefl = new \ReflectionProperty($eventBus, 'events');
        $eventBusEventsRefl->setAccessible(true);
        $eventBusEvents = $eventBusEventsRefl->getValue($eventBus);

        $this->assertNotSame(
            $commandBusEvents, $eventBusEvents,
            'CommandBus and EventBus should not have the same instance of the ActionEventEmitter'
        );

    }

    /**
     * @test
     */
    public function user_register_command_can_be_fired()
    {
        /** @var ProophEventStoreContext $es */
        $es = $this->objectManager->get(ProophEventStoreContext::class);

        /** @var CommandRouter $router */
        $cr = $es->commandBus()->commandRouter();
        $cr->route(RegisterUser::class)->to($this->objectManager->get(RegisterUserHandler::class));
        $cr->route(ChangeEmail::class)->to($this->objectManager->get(ChangeEmailHandler::class));

        $connection = $this->objectManager->create(\Magento\Framework\App\ResourceConnection::class)->getConnection();

        $tableName = 'event_stream_prooph_test_user';
        if ($connection->isTableExists($tableName)) {
            $connection->dropTable($tableName);
        }

        $sql = <<<EOT
CREATE TABLE `$tableName` (
  `no` bigint(20) NOT NULL AUTO_INCREMENT,
  `event_id` char(36) COLLATE utf8_bin NOT NULL,
  `event_name` varchar(100) COLLATE utf8_bin NOT NULL,
  `payload` json NOT NULL,
  `metadata` json NOT NULL,
  `created_at` datetime(6) NOT NULL,
  `aggregate_version` int(11) unsigned GENERATED ALWAYS AS (json_extract(`metadata`,'$._aggregate_version')) STORED NOT NULL,
  `aggregate_id` char(36) COLLATE utf8_bin GENERATED ALWAYS AS (json_unquote(json_extract(`metadata`,'$._aggregate_id'))) STORED NOT NULL,
  `aggregate_type` varchar(150) COLLATE utf8_bin GENERATED ALWAYS AS (json_unquote(json_extract(`metadata`,'$._aggregate_type'))) STORED NOT NULL,
  PRIMARY KEY (`no`),
  UNIQUE KEY `ix_event_id` (`event_id`),
  UNIQUE KEY `ix_unique_event` (`aggregate_type`,`aggregate_id`,`aggregate_version`),
  KEY `ix_query_aggregate` (`aggregate_type`,`aggregate_id`,`no`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
EOT;
        $connection->query($sql);

        $userId = Uuid::uuid4();
        $es->commandBus()->dispatch(new RegisterUser([
            'id' => $userId->toString(),
            'email' => 'random@email.com',
            'password' => 'test'
        ]));

        for ($i = 0; $i < 5; $i++) {
            $es->commandBus()->dispatch(new ChangeEmail([
                'email' => 'random' . $i . '@email.com',
                'id' => $userId->toString()
            ]));
        }

        /** @var UserRepository $userRepository */
        $userRepository = $this->objectManager->get(UserRepository::class);
        $user = $userRepository->get($userId->toString());

        $this->assertInstanceOf(User::class, $user);
    }
}
