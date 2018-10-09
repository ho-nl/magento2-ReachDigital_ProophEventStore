<?php
declare(strict_types=1);


namespace ReachDigital\ProophEventStore\Test\Integration\Fixtures\Infrastructure;


use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use ReachDigital\ProophEventStore\Infrastructure\Pdo\Connection;

class PdoTest extends TestCase
{
    /** @var \Magento\Framework\ObjectManagerInterface */
    private $objectManager;

    protected function setUp()
    {
        $this->objectManager = Bootstrap::getObjectManager();
    }

    public function test_pdo_instance_should_work()
    {
        /** @var Connection $pdo */
        $pdo = $this->objectManager->get(Connection::class);
        $this->assertInstanceOf(\PDO::class, $pdo);
        $this->assertEquals('1', $pdo->query('SELECT 1')->execute());
    }
}
