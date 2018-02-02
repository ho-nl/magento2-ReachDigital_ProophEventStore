<?php


namespace ReachDigital\ProophEventStore\Test\Integration\Command;

use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use Prooph\ServiceBus\Plugin\Router\CommandRouter;
use ReachDigital\ProophEventStore\Infrastructure\CommandBus;
use ReachDigital\ProophEventStore\Infrastructure\EventBus;
use ReachDigital\ProophEventStore\Test\Integration\Fixtures\Infrastructure\UserRepository;
use ReachDigital\ProophEventStore\Test\Integration\Fixtures\Model\Command\ChangeEmail;
use ReachDigital\ProophEventStore\Test\Integration\Fixtures\Model\Command\RegisterUser;
use ReachDigital\ProophEventStore\Test\Integration\Fixtures\Model\Handler\ChangeEmailHandler;
use ReachDigital\ProophEventStore\Test\Integration\Fixtures\Model\Handler\RegisterUserHandler;
use ReachDigital\ProophEventStore\Test\Integration\Fixtures\User;

class RegisterUserTest extends TestCase
{
    /** @var \Magento\Framework\ObjectManagerInterface */
    private $objectManager;

    protected function setUp()
    {
        $this->objectManager = Bootstrap::getObjectManager();
    }

    //<type name="Prooph\ServiceBus\Plugin\Router\CommandRouter">
    //    <arguments>
    //        <argument name="messageMap" xsi:type="array">
    //            <item name="ReachDigital\ProophEventStore\Test\Integration\Fixtures\Model\Command\RegisterUser" xsi:type="object">ReachDigital\ProophEventStore\Test\Integration\Fixtures\Model\Handler\RegisterUserHandler</item>
    //            <item name="ReachDigital\ProophEventStore\Test\Integration\Fixtures\Model\Command\ChangeEmail" xsi:type="object">ReachDigital\ProophEventStore\Test\Integration\Fixtures\Model\Handler\ChangeEmailHandler</item>
    //        </argument>
    //    </arguments>
    //</type>
    private function commandBusInstance(): CommandBus
    {
        return $this->objectManager->create(CommandBus::class, [
            'commandRouter' => $this->objectManager->create(CommandRouter::class, [
                'messageMap' => [
                    RegisterUser::class => $this->objectManager->get(RegisterUserHandler::class),
                    ChangeEmail::class => $this->objectManager->get(ChangeEmailHandler::class)
                ]
            ])
        ]);
    }

    /**
     * @test
     */
    public function command_and_event_bus_are_correctly_instantiated()
    {
        $commandBus = $this->commandBusInstance();
        $this->assertInstanceOf(CommandBus::class, $commandBus);

        $commandBusEventsRefl = (new \ReflectionProperty($commandBus, 'events'));
        $commandBusEventsRefl->setAccessible(true);
        $commandBusEvents = $commandBusEventsRefl->getValue($commandBus);


        /** @var EventBus $commandBus */
        $eventBus = $this->objectManager->get(EventBus::class);
        $this->assertInstanceOf(EventBus::class, $eventBus);


        $eventBusEventsRefl = (new \ReflectionProperty($eventBus, 'events'));
        $eventBusEventsRefl->setAccessible(true);
        $eventBusEvents = $eventBusEventsRefl->getValue($eventBus);

        $this->assertFalse($commandBusEvents === $eventBusEvents, 'CommandBus and EventBus should not have the same instance of the ActionEventEmitter');

    }

    /**
     * @test
     */
    public function user_register_command_can_be_fired()
    {
        $commandBus = $this->commandBusInstance();
        $eventBus = $this->objectManager->get(EventBus::class);

        $userId = uniqid();
        $commandBus->dispatch(new RegisterUser([
            'id' => $userId,
            'email' => 'random@email.com',
            'password' => 'test'
        ]));

        for ($i = 0; $i < 5; $i++) {
            $commandBus->dispatch(new ChangeEmail([
                'email' => 'random' . $i . '@email.com',
                'id' => $userId
            ]));
        }

        /** @var UserRepository $userRepository */
        $userRepository = $this->objectManager->get(UserRepository::class);
        $user = $userRepository->get($userId);

        $this->assertInstanceOf(User::class, $user);
    }
}
