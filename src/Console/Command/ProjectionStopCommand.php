<?php

declare(strict_types=1);

namespace ReachDigital\ProophEventStore\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ProjectionStopCommand extends AbstractProjectionCommand
{
    protected function configure()
    {
        parent::configure();
        $this->setName('event-store:projection:stop')->setDescription('Stops a projection');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(
            sprintf('<action>Stopping projection <highlight>%s</highlight></action>', $this->projectionName)
        );
        $this->projectionContext->projectionManager()->stopProjection($this->projectionName);
    }
}
