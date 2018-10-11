<?php
declare(strict_types=1);

namespace ReachDigital\ProophEventStore\Setup;

use Magento\Framework\Filesystem\Glob;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use ReachDigital\ProophEventStore\Infrastructure\Pdo\DbTypeResolver;

class InstallSchema implements InstallSchemaInterface
{

    /**
     * @var DbTypeResolver
     */
    private $dbTypeResolver;

    public function __construct(
        DbTypeResolver $dbTypeResolver
    ) {
        $this->dbTypeResolver = $dbTypeResolver;
    }

    /** @noinspection ReturnTypeCanBeDeclaredInspection */
    /**
     * Installs DB schema for a module
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $packageDir = \ComposerLocator::getPath('prooph/pdo-event-store');
        $sqlFiles = Glob::glob("{$packageDir}/scripts/{$this->dbTypeResolver->get()->toString()}/*.sql");

        $packageDir = \ComposerLocator::getPath('prooph/pdo-snapshot-store');
        $sqlFiles[] = "{$packageDir}/scripts/mysql_snapshot_table.sql";

        foreach ($sqlFiles as $sqlFile) {
            $setup->run(\file_get_contents($sqlFile));
        }

        $setup->endSetup();
    }
}
