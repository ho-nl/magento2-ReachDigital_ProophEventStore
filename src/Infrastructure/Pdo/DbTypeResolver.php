<?php
/**
 * Copyright © Reach Digital (https://www.reachdigital.io/)
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace ReachDigital\ProophEventStore\Infrastructure\Pdo;

class DbTypeResolver
{
    public const DB_TYPE_MYSQL = 'mysql';
    public const DB_TYPE_MARIADB = 'mariadb';

    /** @var Connection  */
    private $connection;

    /** @var string */
    private $dbType;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function get() : string
    {
        $info = $this->connection->query("SHOW VARIABLES like '%version%'")->fetchAll(\PDO::FETCH_KEY_PAIR);
        if (version_compare($info['version'], '10.2.11', '>=')) {
            $this->dbType = self::DB_TYPE_MARIADB;
        } elseif (version_compare($info['version'], '5.7.9', '>=') && version_compare($info['version'], '10', '<')) {
            $this->dbType = self::DB_TYPE_MYSQL;
        } else {
            throw new \RuntimeException(sprintf(
                'Database version not supported, see https://github.com/prooph/pdo-event-store#requirements: %s %s',
                $info['version'],
                $info['version_comment']
            ));
        }

        return self::DB_TYPE_MYSQL;
    }
}
