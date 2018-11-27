<?php declare(strict_types=1);
/**
 * Copyright © Reach Digital (https://www.reachdigital.io/)
 * See LICENSE.txt for license details.
 */
namespace ReachDigital\ProophEventStore\Test\Integration\Infrastructure;

use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use Prooph\EventStore\Pdo\MySqlEventStore;

class EventStoreTest extends TestCase
{
    /**
     * @test
     */
    public function should_instantiate_event_store()
    {
        $mysqlEventStore = Bootstrap::getObjectManager()->create(MySqlEventStore::class);
    }
}
