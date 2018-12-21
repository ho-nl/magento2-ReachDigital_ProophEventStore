<?php
declare(strict_types=1);


namespace ReachDigital\ProophEventStore\Test\Integration\Fixtures\Infrastructure;

use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

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
        $userRepository = $this->objectManager->get(UserRepository::class);
        $this->assertInstanceOf(UserRepository::class, $userRepository);
    }
}
