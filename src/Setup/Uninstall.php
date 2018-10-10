<?php
/**
 * Copyright © Reach Digital (https://www.reachdigital.io/)
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace ReachDigital\ProophEventStore\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UninstallInterface;

class Uninstall implements UninstallInterface
{

    /**
     * Invoked when remove-data flag is set during module uninstall.
     *
     * @param SchemaSetupInterface   $setup
     * @param ModuleContextInterface $context
     *
     * @return void
     */
    public function uninstall(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if ($setup->getConnection()->isTableExists('event_streams')) {
            $setup->getConnection()->dropTable('event_streams');
        }
        if ($setup->getConnection()->isTableExists('projections')) {
            $setup->getConnection()->dropTable('projections');
        }
        if ($setup->getConnection()->isTableExists('snapshots')) {
            $setup->getConnection()->dropTable('snapshots');
        }

        $setup->endSetup();
    }
}
