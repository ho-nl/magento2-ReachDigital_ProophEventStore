<?php


namespace ReachDigital\ProophEventStore\Test\Integration\Fixtures\Infrastructure;

use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use ReachDigital\ProophEventStore\Test\Integration\Fixtures\Api\UserRepositoryInterface;
use ReachDigital\ProophEventStore\Test\Integration\Fixtures\Infrastructure\UserRepository;

class UserRepositoryTest extends TestCase
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
    public function user_repository_initialisation()
    {
        $userRepository = $this->objectManager->get(UserRepositoryInterface::class);
        $this->assertInstanceOf(UserRepository::class, $userRepository);
    }
}
