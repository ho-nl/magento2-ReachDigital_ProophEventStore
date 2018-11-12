<?php
/**
 * Copyright © Reach Digital (https://www.reachdigital.io/)
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);
namespace ReachDigital\ProophEventStore\Infrastructure\Projection;

use Magento\Framework\Model\ResourceModel\Db\Context;

class SimpleResourceModel extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /** @var TableName */
    private $tableName;

    /** @var PrimaryKey */
    private $primaryKey;

    public function __construct(
        Context $context,
        TableName $tableName,
        PrimaryKey $primaryKey,
        ?string $connectionName = null
    ) {
        $this->tableName = $tableName;
        $this->primaryKey = $primaryKey;
        parent::__construct($context, $connectionName);
    }

    /** @noinspection MagicMethodsValidityInspection */
    protected function _construct()
    {
        $this->_init($this->tableName, $this->primaryKey);
    }
}
