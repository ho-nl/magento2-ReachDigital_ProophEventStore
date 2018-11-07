<?php
/**
 * Copyright © Reach Digital (https://www.reachdigital.io/)
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);
namespace ReachDigital\ProophEventStore\Console\Command;

use Magento\Framework\Shell;
use Prooph\EventStore\Projection\ProjectionManager;
use Prooph\EventStore\Projection\Projector;
use ReachDigital\ProophEventStore\Infrastructure\Projection\ProjectionContextPool;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ProjectionRunAllCommand extends \Symfony\Component\Console\Command\Command
{
    public const OPTION_RUN_ONCE = 'run-once';

    /**
     * @var ProjectionContextPool
     */
    private $projectionContextPool;

    /**
     * @var ProjectionManager
     */
    private $projectionManager;

    /**
     * @var Shell
     */
    private $shell;

    public function __construct(
        ProjectionContextPool $projectionContextPool,
        ProjectionManager $projectionManager,
        Shell $shell,
        ?string $name = null
    ) {
        parent::__construct($name);
        $this->projectionContextPool = $projectionContextPool;
        $this->projectionManager = $projectionManager;
        $this->shell = $shell;
    }

    protected function configure()
    {
        parent::configure();
        $this
            ->setName('event-store:projection:run-all')
            ->setDescription('Displays the state of all projections')
            ->addOption(static::OPTION_RUN_ONCE, 'o', InputOption::VALUE_NONE, 'Loop the projection only once, then exit');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $keepRunning = ! $input->getOption(static::OPTION_RUN_ONCE);
        foreach ($this->projectionContextPool->all() as $projectionContext) {
            $output->writeln(
                sprintf(
                    '<action>Starting projection <highlight>%s</highlight>. Keep running: <highlight>%s</highlight></action>',
                    $projectionContext->name(),
                    $keepRunning === true ? 'enabled' : 'disabled'
                )
            );


            $this->shell->execute('./bin/magento event-store:projection:run');


            $pid = pcntl_fork();
            if ($pid === -1) {
                exit("Error forking...\n");
            }

            if ($pid === 0) {
                $projector = $projectionContext->projector([Projector::OPTION_PCNTL_DISPATCH => true]);
                $projection = $projectionContext->projection()->project($projector);
                $projection->run($keepRunning);
                exit();
            }
        }
    }
}
