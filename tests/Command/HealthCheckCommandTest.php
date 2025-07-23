<?php

namespace Pbxg33k\Test\FlareSolverrBundle\Command;

use Pbxg33k\FlareSolverrBundle\Client\FlareSolverrClient;
use Pbxg33k\FlareSolverrBundle\Command\HealthCheckCommand;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class HealthCheckCommandTest extends TestCase
{
    private MockObject|FlareSolverrClient $flareSolverrClient;

    protected function setUp(): void
    {
        $this->flareSolverrClient = $this->createMock(FlareSolverrClient::class);
    }

    public function testExecuteWithHealthyServer()
    {
        $this->flareSolverrClient->expects($this->once())
            ->method('checkHealth')
            ->willReturn(true);

        $command = new ExtendedCommand($this->flareSolverrClient);
        $input = $this->createMock(InputInterface::class);
        $output = $this->createMock(OutputInterface::class);

        $result = $command->execute($input, $output);

        $this->assertEquals(Command::SUCCESS, $result);
    }

    public function testExecuteWithUnhealthyServer()
    {
        $this->flareSolverrClient->expects($this->once())
            ->method('checkHealth')
            ->willReturn(false);

        $command = new ExtendedCommand($this->flareSolverrClient);
        $input = $this->createMock(InputInterface::class);
        $output = $this->createMock(OutputInterface::class);

        $result = $command->execute($input, $output);

        $this->assertEquals(Command::FAILURE, $result);
    }

    protected function tearDown(): void
    {
        unset($this->flareSolverrClient);
    }
}

class ExtendedCommand extends HealthCheckCommand
{
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        return parent::execute($input, $output);
    }
}
