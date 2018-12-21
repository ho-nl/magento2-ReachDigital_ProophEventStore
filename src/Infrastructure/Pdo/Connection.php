<?php
/**
 * Copyright © Reach Digital (https://www.reachdigital.io/)
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace ReachDigital\ProophEventStore\Infrastructure\Pdo;

use Magento\Framework\App\ResourceConnection;
use Zend_Db_Adapter_Abstract;

class Connection extends \PDO
{
    public function __construct(
        ResourceConnection $resource
    ) {
        $connection = $resource->getConnection();
        if (! $connection instanceof Zend_Db_Adapter_Abstract) {
            throw new \RuntimeException(
                sprintf('Class %s should inherit Zend_Db_Adapter_Abstract'. \get_class($connection))
            );
        }
        /** @var Zend_Db_Adapter_Abstract $connection */
        $conf = $connection->getConfig();
        $dsn = $this->_dsn($connection->getConfig());

        parent::__construct($dsn, $conf['username'], $conf['password'], $conf['options']);
        $this->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    /**
     * Creates a \PDO DSN
     *
     * @param array $dsn
     * @return string
     */
    private function _dsn(array $dsn): string
    {
        // don't pass the username, password, charset, persistent and driver_options in the DSN
        unset(
            $dsn['username'],
            $dsn['password'],
            $dsn['options'],
            $dsn['charset'],
            $dsn['persistent'],
            $dsn['driver_options']
        );

        // use all remaining parts in the DSN
        foreach ($dsn as $key => $val) {
            $dsn[$key] = "$key=$val";
        }

        return 'mysql' . ':' . implode(';', $dsn);
    }
}
