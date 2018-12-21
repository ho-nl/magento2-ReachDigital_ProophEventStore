<?php
/**
 * Copyright © Reach Digital (https://www.reachdigital.io/)
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);
namespace ReachDigital\ProophEventStore\Infrastructure\Projection;

use Magento\Framework\App\ResourceConnection;


class SimpleReadModel implements \Prooph\EventStore\Projection\ReadModel
{
    public const INSERT = 'insert';
    public const UPDATE = 'update';
    public const DELETE = 'delete';

    private $operations = [self::INSERT, self::UPDATE, self::DELETE];

    /** @var ResourceConnection */
    private $resourceConnection;

    /** @var TableName */
    private $tableName;

    /** @var PrimaryKey */
    private $primaryKey;

    /** @var array */
    private $stack = [];

    public function __construct(
        ResourceConnection $resourceConnection,
        TableName $tableName,
        PrimaryKey $primaryKey
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->tableName = $tableName;
        $this->primaryKey = $primaryKey;
    }

    public function init(): void
    {
        if (! $this->isInitialized()) {
            throw new \RuntimeException('Table is not initialized, should be created with db_schema.xml');
        }
    }

    public function isInitialized(): bool
    {
        return (bool)$this->resourceConnection->getConnection()->showTableStatus((string)$this->tableName);
    }

    public function reset(): void
    {
        if (! $this->isInitialized()) {
            return;
        }
        $this->resourceConnection->getConnection()->truncateTable((string)$this->tableName);
    }

    public function delete(): void
    {
        throw new \RuntimeException('Can not delete projection, should be deleted by db_schema.xml');
    }

    public function stack(string $operation, ...$args): void
    {
        if (!\in_array($operation, $this->operations, true)) {
            throw new \InvalidArgumentException(
                'Stack operation should be one of: self::INSERT, self::UPDATE, self::DELETE'
            );
        }
        $this->stack[] = [$operation, $args];
    }

    public function persist(): void
    {
        foreach ($this->stack as [$operation, $args]) {
            $this->{$operation}(...$args);
        }

        $this->stack = [];
    }

    protected function insert(array $data): void
    {
        $this->resourceConnection->getConnection()->insert((string) $this->tableName, $data);
    }

    protected function update(array $data): void
    {
        $this->resourceConnection->getConnection()->update(
            (string) $this->tableName,
            $data,
            [
                "{$this->primaryKey} = ?" => $data[(string) $this->primaryKey]
            ]
        );
    }

    protected function remove(array $data): void
    {
        $this->resourceConnection->getConnection()->delete(
            (string) $this->tableName,
            $data[(string) $this->primaryKey]
        );
    }
}
