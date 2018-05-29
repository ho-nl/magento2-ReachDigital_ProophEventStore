<?php
/**
 * Copyright (c) Reach Digital (https://www.reachdigital.nl/)
 * See README.md for license details.
 */

declare(strict_types=1);

namespace ReachDigital\ProophEventStore\Infrastructure;

use Magento\Framework\App\ResourceConnection;

//@todo move to Infrastructure\Database namespace
class MysqlXdevapi
{
    /** @var mysql_xdevap\Session */
    private $session;
    /**
     * @var
     */
    private $uri;
    private $schemaName;

    public function __construct(
        ResourceConnection $resource
    ) {
        $connection = $resource->getConnection();
        if (! $connection instanceof \Zend_Db_Adapter_Abstract) {
            throw new \RuntimeException(
                sprintf('Class %s should inherit Zend_Db_Adapter_Abstract'. \get_class($connection))
            );
        }
        /** @var \Zend_Db_Adapter_Abstract $connection */
        $conf = $connection->getConfig();
        $this->schemaName = $conf['dbname'];
        $uri = $this->_uri($connection->getConfig());

        $this->session = \mysql_xdevapi\getSession($uri);

    }

    public function session(): \mysql_xdevapi\Session
    {
        return $this->session;
    }

    public function schema(): \mysql_xdevapi\Schema
    {
        return $this->session()->getSchema($this->schemaName);
    }

    /**
     * Creates a \PDO DSN
     *
     * @param array $conf
     * @return string
     */
    private function _uri(array $conf): string
    {
        $uri = \Zend\Uri\UriFactory::factory('');
        $uri->setScheme('mysqlx');
        $uri->setUserInfo($conf['username'].':'.$conf['password']);

        $uri->setHost($conf['host']);
        $uri->setPort($conf['port'] ?? 33060);
        $uri->setPath('/'.$conf['dbname']);

        return $uri->toString();
    }
}
