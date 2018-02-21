<?php

declare(strict_types=1);

namespace ReachDigital\ProophEventStore\Console\Command;

use ReachDigital\ProophEventStore\Infrastructure\ProjectionContext;
use ReachDigital\ProophEventStore\Infrastructure\ProjectionContextPool;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractProjectionCommand extends Command
{
    use FormatsOutput;

    protected const ARGUMENT_PROJECTION_NAME = 'projection-name';

    /**
     * @var ProjectionContextPool
     */
    protected $projectionContextPool;

    /**
     * @var string
     */
    protected $projectionName;

    /**
     * @var ProjectionContext
     */
    protected $projectionContext;

    public function __construct(
        ProjectionContextPool $projectionContextPool,
        $name = null
    ) {
        parent::__construct($name);
        $this->projectionContextPool = $projectionContextPool;
    }


    protected function configure()
    {
        $this->addArgument(static::ARGUMENT_PROJECTION_NAME, InputArgument::REQUIRED, 'The name of the Projection');
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $input->validate();

        $this->formatOutput($output);

        $this->projectionName = $input->getArgument(static::ARGUMENT_PROJECTION_NAME);
        $this->projectionContext = $this->projectionContextPool->get($this->projectionName);

        $output->writeln(sprintf('<header>Initialized projection "%s"</header>', $this->projectionName));
        try {
            $state = $this->projectionContext->projectionManager()->fetchProjectionStatus($this->projectionName)->getValue();
        } catch (\Prooph\EventStore\Exception\RuntimeException $e) {
            $state = 'unknown';
        }
        $output->writeln(sprintf('<action>Current status: <highlight>%s</highlight></action>', $state));
        $output->writeln('====================');
    }
}
