<?php
/**
 * Copyright © Reach Digital (https://www.reachdigital.io/)
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);
namespace ReachDigital\ProophEventStore\Console\Command;

use Magento\Framework\Shell;
use ReachDigital\ProophEventStore\Infrastructure\Projection\ProjectionContextPool;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ProjectionRunAllCommand extends \Symfony\Component\Console\Command\Command
{
    use FormatsOutput;

    public const OPTION_RUN_ONCE = 'run-once';

    /** @var ProjectionContextPool */
    private $projectionContextPool;

    /** @var Shell */
    private $shell;

    /** @var \Symfony\Component\Process\PhpExecutableFinder */
    private $phpExecutableFinder;

    public function __construct(
        ProjectionContextPool $projectionContextPool,
        \Magento\Framework\Process\PhpExecutableFinderFactory $phpExecutableFinderFactory,
        Shell $shell,
        ?string $name = null
    ) {
        parent::__construct($name);
        $this->projectionContextPool = $projectionContextPool;
        $this->phpExecutableFinder = $phpExecutableFinderFactory->create();
        $this->shell = $shell;
    }

    protected function configure() : void
    {
        parent::configure();
        $this
            ->setName('event-store:projection:run-all')
            ->setDescription('Displays the state of all projections')
            ->addOption(static::OPTION_RUN_ONCE, 'o', InputOption::VALUE_NONE, 'Loop the projection only once, then exit');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->formatOutput($output);

        $keepRunning = ! $input->getOption(static::OPTION_RUN_ONCE);
        foreach ($this->projectionContextPool->all() as $projectionContext) {
            $output->writeln(
                sprintf(
                    '<action>Starting projection <highlight>%s</highlight> in background. Keep running: <highlight>%s</highlight></action>',
                    $projectionContext->name(),
                    $keepRunning === true ? 'enabled' : 'disabled'
                )
            );

            $phpPath = $this->phpExecutableFinder->find() ?: 'php';
            $runOnce = $input->getOption(static::OPTION_RUN_ONCE) ? '-o' : '';
            $this->shell->execute(
                "{$phpPath} %s event-store:projection:run {$runOnce} %s > /dev/null 2>&1 &",
                [
                    BP . '/bin/magento',
                    $projectionContext->name()
                ]
            );
        }
    }
}
