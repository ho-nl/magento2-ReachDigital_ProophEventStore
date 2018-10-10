<?php
/**
 * Copyright © Reach Digital (https://www.reachdigital.io/)
 * See LICENSE.txt for license details.
 */
namespace ReachDigital\ProophEventStore\Infrastructure\EventStore;

use Prooph\EventStore\Pdo\PersistenceStrategy;
use ReachDigital\ProophEventStore\Infrastructure\Pdo\DbTypeResolver;

class PersistenceStrategyFactory
{
    /** @var PersistenceStrategy */
    private $instance;

    public function __construct(
        DbTypeResolver $dbTypeResolver,
        MysqlSingleStreamStrategyProxyFactory $mysqlSingleStreamStrategyProxFactory,
        MariaDbSingleStreamStrategyProxyFactory $mariaDbSingleStreamStrategyProxyFactory
    ) {
        if ($dbTypeResolver->get() === DbTypeResolver::DB_TYPE_MARIADB) {
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