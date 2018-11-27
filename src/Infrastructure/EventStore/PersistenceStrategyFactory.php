<?php
/**
 * Copyright © Reach Digital (https://www.reachdigital.io/)
 * See LICENSE.txt for license details.
 */
namespace ReachDigital\ProophEventStore\Infrastructure\EventStore;

use Prooph\EventStore\Pdo\PersistenceStrategy;
use ReachDigital\ProophEventStore\Infrastructure\Pdo\DbType;
use ReachDigital\ProophEventStore\Infrastructure\Pdo\DbTypeResolver;

class PersistenceStrategyFactory
{
    /** @var PersistenceStrategy */
    private $instance;

    public function __construct(
        DbTypeResolver $dbTypeResolver,
        MysqlSingleStreamStrategyFactory $mysqlSingleStreamStrategyProxFactory,
        MariaDbSingleStreamStrategyFactory $mariaDbSingleStreamStrategyProxyFactory
    ) {
        if ($dbTypeResolver->get()->equals(DbType::mySql())) {
            $this->instance = $mysqlSingleStreamStrategyProxFactory->create();
        } else {
            $this->instance = $mariaDbSingleStreamStrategyProxyFactory->create();
        }

    }

    public function get(): PersistenceStrategy
    {
        return $this->instance;
    }
}
