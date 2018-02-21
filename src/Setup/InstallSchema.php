<?php
declare(strict_types=1);


namespace ReachDigital\ProophEventStore\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * Installs DB schema for a module
     * @todo replace with reference to the libraries direct .sql files
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $setup->run('CREATE TABLE `event_streams` (
  `no` BIGINT(20) NOT NULL AUTO_INCREMENT,
  `real_stream_name` VARCHAR(150) NOT NULL,
  `stream_name` CHAR(41) NOT NULL,
  `metadata` JSON,
  `category` VARCHAR(150),
  PRIMARY KEY (`no`),
  UNIQUE KEY `ix_rsn` (`real_stream_name`),
  KEY `ix_cat` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;');

        $setup->run('CREATE TABLE `projections` (
  `no` BIGINT(20) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(150) NOT NULL,
  `position` JSON,
  `state` JSON,
  `status` VARCHAR(28) NOT NULL,
  `locked_until` CHAR(26),
  PRIMARY KEY (`no`),
  UNIQUE KEY `ix_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;');

        $setup->run('CREATE TABLE `snapshots` (
  `aggregate_id` VARCHAR(150) NOT NULL,
  `aggregate_type` VARCHAR(150) NOT NULL,
  `last_version` INT(11) NOT NULL,
  `created_at` CHAR(26) NOT NULL,
  `aggregate_root` BLOB,
  UNIQUE KEY `ix_aggregate_id` (`aggregate_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;');

        $setup->endSetup();
    }
}