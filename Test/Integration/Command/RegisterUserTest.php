<?php


namespace ReachDigital\ProophEventStore\Test\Integration\Command;

use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use ReachDigital\ProophEventStore\Infrastructure\CommandBus;
use ReachDigital\ProophEventStore\Infrastructure\EventBus;
use ReachDigital\ProophEventStore\Test\Integration\Fixtures\Infrastructure\UserRepository;
use ReachDigital\ProophEventStore\Test\Integration\Fixtures\Model\Command\ChangeEmail;
use ReachDigital\ProophEventStore\Test\Integration\Fixtures\Model\Command\RegisterUser;
use ReachDigital\ProophEventStore\Test\Integration\Fixtures\User;

class RegisterUserTest extends TestCase
{
    /** @var \Magento\Framework\ObjectManagerInterface */
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
        /** @var CommandBus $commandBus */
        $commandBus = $this->objectManager->get(CommandBus::class);
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
        /** @var CommandBus $commandBus */
        $commandBus = $this->objectManager->get(CommandBus::class);
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
