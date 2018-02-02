<?php

namespace ReachDigital\ProophEventStore\Test\Integration\Projection;

use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use ReachDigital\ProophEventStore\Infrastructure\ProjectionContext;
use ReachDigital\ProophEventStore\Infrastructure\ProjectionContextPool;

class UserProjectionTest extends TestCase
{

    /** @var  \Magento\Framework\ObjectManagerInterface */
    private $objectManager;

    protected function setUp() {
        $this->objectManager = Bootstrap::getObjectManager();
    }

    /**
     * @test
     */
    public function user_projection_should_be_registered()
    {
        $projectionPool = $this->objectManager->get(ProjectionContextPool::class);
        $this->assertInstanceOf(ProjectionContext::class, $projectionPool->get('jira_user'));
    }

}
