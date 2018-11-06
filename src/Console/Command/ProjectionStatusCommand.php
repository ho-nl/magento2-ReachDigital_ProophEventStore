<?php
/**
 * Copyright © Reach Digital (https://www.reachdigital.io/)
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);
namespace ReachDigital\ProophEventStore\Console\Command;

use Prooph\EventStore\Projection\ProjectionManager;
use ReachDigital\ProophEventStore\Infrastructure\Projection\ProjectionContextPool;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ProjectionStatusCommand extends \Symfony\Component\Console\Command\Command
{

    /**
     * @var ProjectionContextPool
     */
    private $projectionContextPool;

    /**
     * @var ProjectionManager
     */
    private $projectionManager;

    public function __construct(
        ProjectionContextPool $projectionContextPool,
        ProjectionManager $projectionManager,
        ?string $name = null
    ) {
        parent::__construct($name);
        $this->projectionContextPool = $projectionContextPool;
        $this->projectionManager = $projectionManager;
    }

    protected function configure()
    {
        parent::configure();
        $this
            ->setName('event-store:projection:status')
            ->setDescription('Displays the state of all projections');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $table = (new Table($output))->setHeaders(['Projection', 'Position', 'Status', 'State']);

        foreach ($this->projectionContextPool->all() as $projectionContext) {
            $streamPositions = $this->projectionManager->fetchProjectionStreamPositions($projectionContext->name());
            $streamPositionOutput = [];
            foreach ($streamPositions as $stream => $position) {
                $streamPositionOutput[] = "{$stream}: {$position}";
            }
            $streamPositionOutput = implode("\n", $streamPositionOutput);

            $table->addRow([
                $projectionContext->name(),
                $streamPositionOutput,
                $this->projectionManager->fetchProjectionStatus($projectionContext->name())->getValue(),
                \json_encode($this->projectionManager->fetchProjectionState($projectionContext->name())),
            ]);
        }

        $table->render();
    }
}
