<?php

declare(strict_types=1);

namespace ReachDigital\ProophEventStore\Console\Command;

use Prooph\EventStore\Pdo\Projection\MySqlProjectionManager;
use ReachDigital\ProophEventStore\Infrastructure\ProjectionContext;
use ReachDigital\ProophEventStore\Infrastructure\ProjectionContextPool;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ProjectionNamesCommand extends Command
{
    use FormatsOutput;

    private const ARGUMENT_FILTER = 'filter';
    private const OPTION_REGEX = 'regex';
    private const OPTION_LIMIT = 'limit';
    private const OPTION_OFFSET = 'offset';
    private const OPTION_MANAGER = 'manager';
    /**
     * @var ProjectionContextPool
     */
    private $projectionContextPool;
    /**
     * @var MySqlProjectionManager
     */
    private $mySqlProjectionManager;


    public function __construct(
        ProjectionContextPool $projectionContextPool,
        MySqlProjectionManager $mySqlProjectionManager,
        $name = null
    ) {
        parent::__construct($name);
        $this->projectionContextPool = $projectionContextPool;
        $this->mySqlProjectionManager = $mySqlProjectionManager;
    }


    protected function configure()
    {
        $this
            ->setName('event-store:projection:names')
            ->setDescription('Shows a list of all projection names. Can be filtered.')
            ->addArgument(self::ARGUMENT_FILTER, InputArgument::OPTIONAL, 'Filter by this string')
            ->addOption(self::OPTION_REGEX, 'r', InputOption::VALUE_NONE, 'Enable regex syntax for filter')
            ->addOption(self::OPTION_LIMIT, 'l', InputOption::VALUE_REQUIRED, 'Limit the result set', 20)
            ->addOption(self::OPTION_OFFSET, 'o', InputOption::VALUE_REQUIRED, 'Offset for result set', 0);
//            ->addOption(self::OPTION_MANAGER, 'm', InputOption::VALUE_REQUIRED, 'Manager for result set', null);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->formatOutput($output);

        $filter = $input->getArgument(self::ARGUMENT_FILTER);
        $regex = $input->getOption(self::OPTION_REGEX);

        $output->write(sprintf('<action>Projection names'));
        if ($filter) {
            $output->write(sprintf(' filter <highlight>%s</highlight>', $filter));
        }
        if ($regex) {
            $output->write(' <comment>regex enabled</comment>');
            $method = 'fetchProjectionNamesRegex';
        } else {
            $method = 'fetchProjectionNames';
        }
        $output->writeln('</action>');

        $names = [];
        $offset = (int) $input->getOption(self::OPTION_OFFSET);
        $limit = (int) $input->getOption(self::OPTION_LIMIT);

        $names = array_map(function(ProjectionContext $projectionContext) {
            return [
                'name' => $projectionContext->name(),
                'projection' => \get_class($projectionContext->projection()),
                'projector' => \get_class($projectionContext->projector())
            ];
        }, $this->projectionContextPool->all());

//        if (count($names) > $offset) {
//            $projectionNames = $this->mySqlProjectionManager->$method($filter, $limit - (count($names) - $offset));
//        } else {
//            $projectionNames = $this->mySqlProjectionManager->$method($filter);
//        }
//
//        foreach ($projectionNames as $projectionName) {
//            $names[$projectionName] = ['initialized'];
//        }

        $names = array_slice($names, $offset, $limit);

        $table = new Table($output);
        $table
            ->setHeaders(['name', 'projection', 'projector'])
            ->setRows($names);

        $table->render();
    }
}
