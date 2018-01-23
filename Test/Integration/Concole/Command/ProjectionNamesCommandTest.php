<?php


namespace ReachDigital\ProophEventStore\Test\Integration\Console\Command;


use Magento\Framework\ObjectManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use ReachDigital\ProophEventStore\Console\Command\ProjectionNamesCommand;
use Symfony\Component\Console\Tester\CommandTester;

class ProjectionNamesCommandTest extends TestCase
{
    /** @var ObjectManagerInterface */
    private $objectManager;
    
    /** @var ProjectionNamesCommand */
    private $command;
    
    /** @var CommandTester */
    private $tester;

    protected function setUp() {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->command = $this->objectManager->create(ProjectionNamesCommand::class);
        $this->tester = new CommandTester($this->command);
        
    }
    
    /**
     * @test get projection names
     */
    public function can_get_projection_names()
    {
        $this->tester->execute([]);
    }
}
