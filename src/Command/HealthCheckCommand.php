<?php

namespace Pbxg33k\FlareSolverrBundle\Command;

use Pbxg33k\FlareSolverrBundle\Client\FlareSolverrClient;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class HealthCheckCommand extends Command
{
    public function __construct(
        private readonly FlareSolverrClient $flareSolverrClient,
        private string $flareSolverrRootUrl = '',
        ?string $name = null
    )
    {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            ->setName('flare_solverr:healthcheck')
            ->setDescription('Check the health of the FlareSolverr server.');

    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->info("Checking FlareSolverr server health for {$this->flareSolverrRootUrl}...");

        // Check the health of the FlareSolverr server
        $healthCheckResult = $this->flareSolverrClient->checkHealth();

        if(!$healthCheckResult) {
            $io->error('FlareSolverr server is not healthy or not reachable.');
            return Command::FAILURE;
        }
        $io->success('FlareSolverr server is healthy and reachable!');

        return Command::SUCCESS;
    }
}
