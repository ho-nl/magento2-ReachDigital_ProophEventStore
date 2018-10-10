<?php
/**
 * Copyright © Reach Digital (https://www.reachdigital.io/)
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace ReachDigital\ProophEventStore\Infrastructure\Pdo;

class DbTypeResolver
{
    /** @var Connection  */
    private $connection;

    /** @var DbType */
    private $dbType;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function get(): DbType
    {
        if ($this->dbType === null) {
            $this->dbType = $this->resolveDbType();
        }

        return $this->dbType;
    }

    private function resolveDbType(): DbType
    {
        $info = $this->connection->query("SHOW VARIABLES like 'version%'")->fetchAll(\PDO::FETCH_KEY_PAIR);
        if (version_compare($info['version'], '10.2.11', '>=')) {
            return DbType::mariaDb();
        }

        if (version_compare($info['version'], '5.7.9', '>=') && version_compare($info['version'], '10', '<')) {
            return DbType::mySql();
        }

        throw new \RuntimeException(sprintf(
            'Database version not supported, see https://github.com/prooph/pdo-event-store#requirements: %s %s',
            $info['version'],
            $info['version_comment']
        ));
    }
}
