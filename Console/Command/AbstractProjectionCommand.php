<?php

declare(strict_types=1);

namespace ReachDigital\ProophEventStore\Console\Command;

use ReachDigital\ProophEventStore\Exception\RuntimeException;
use ReachDigital\ProophEventStore\Api\ProjectionInterface;
use Prooph\EventStore\Projection\ProjectionManager;
use Prooph\EventStore\Projection\Projector;
use Prooph\EventStore\Projection\ReadModelProjector;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractProjectionCommand extends Command
{
    use FormatsOutput;

    protected const ARGUMENT_PROJECTION_NAME = 'projection-name';

    /**
     * @var ProjectionManager
     */
    protected $projectionManager;

    /**
     * @var string
     */
    protected $projectionName;

    /**
     * @var ReadModel|null
     */
    protected $readModel;

    /**
     * @var ReadModelProjector|Projector
     */
    protected $projector;

    /**
     * @var ProjectionInterface|ProjectionInterface
     */
    protected $projection;

    public function __construct(
        $name = null
    ) {
        parent::__construct($name);
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

        $container = $this->getContainer();

        if (! $container->has(sprintf('%s.%s.projection_manager', ProophEventStoreExtension::TAG_PROJECTION, $this->projectionName))) {
            throw new RuntimeException(sprintf('ProjectionManager for "%s" not found', $this->projectionName));
        }
        $this->projectionManager = $container->get(sprintf('%s.%s.projection_manager', ProophEventStoreExtension::TAG_PROJECTION, $this->projectionName));

        if (! $container->has(sprintf('%s.%s', ProophEventStoreExtension::TAG_PROJECTION, $this->projectionName))) {
            throw new RuntimeException(sprintf('Projection "%s" not found', $this->projectionName));
        }
        $this->projection = $container->get(sprintf('%s.%s', ProophEventStoreExtension::TAG_PROJECTION, $this->projectionName));

        if ($this->projection instanceof ProjectionInterface) {
            if (! $container->has(sprintf('%s.%s.read_model', ProophEventStoreExtension::TAG_PROJECTION, $this->projectionName))) {
                throw new RuntimeException(sprintf('ReadModel for "%s" not found', $this->projectionName));
            }
            $this->readModel = $container->get(sprintf('%s.%s.read_model', ProophEventStoreExtension::TAG_PROJECTION, $this->projectionName));

            $this->projector = $this->projectionManager->createReadModelProjection($this->projectionName, $this->readModel);
        }

        if ($this->projection instanceof ProjectionInterface) {
            $this->projector = $this->projectionManager->createProjection($this->projectionName);
        }

        if (null === $this->projector) {
            throw new RuntimeException('Projection was not created');
        }
        $output->writeln(sprintf('<header>Initialized projection "%s"</header>', $this->projectionName));
        try {
            $state = $this->projectionManager->fetchProjectionStatus($this->projectionName)->getValue();
        } catch (\Prooph\EventStore\Exception\RuntimeException $e) {
            $state = 'unknown';
        }
        $output->writeln(sprintf('<action>Current status: <highlight>%s</highlight></action>', $state));
        $output->writeln('====================');
    }
}
