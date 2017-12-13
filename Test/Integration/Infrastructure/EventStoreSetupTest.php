<?php


namespace ReachDigital\ProophEventStore\Test\Integration\Model;


use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use ReachDigital\ProophEventStore\Infrastructure\SetupEventStore;

class EventStoreSetupTest extends TestCase
{
    /** @var \Magento\Framework\ObjectManagerInterface */
    private $objectManager;

    protected function setUp()
    {
        $this->objectManager = Bootstrap::getObjectManager();
    }

    public function test_event_store_is_set_up()
    {
        /** @var SetupEventStore $setupEventStore */
        $setupEventStore = $this->objectManager->get(SetupEventStore::class);
        $setupEventStore->setup();

    }
}
