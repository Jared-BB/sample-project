<?php

declare(strict_types=1);

namespace App\Shared\UI\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Process\Process;

#[AsCommand(
    name: 'app:worker:watch',
    description: 'Check if Messenger workers are working.',
)]
class WorkerWatchCommand extends Command
{
    public function __construct(
        private readonly KernelInterface $kernel,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption(
                'restart',
                null,
                InputOption::VALUE_NONE,
                'Restart the workers.'
            )
            ->addOption(
                'workers',
                null,
                InputOption::VALUE_OPTIONAL,
                'Num of workers to launch.',
                1
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $restart = (bool) $input->getOption('restart');

        $numWorkers = (int) $input->getOption('workers');
        $numWorkers = max(1, $numWorkers);
        $numWorkers = min($numWorkers, 10);

        $this->handleWorker('events', $restart, $numWorkers, $output);
        $this->handleWorker('commands', $restart, $numWorkers, $output);

        return Command::SUCCESS;
    }

    private function handleWorker(
        string $transport,
        bool $restart,
        int $numWorkers,
        OutputInterface $output,
    ): void {
        $check = new Process(['pgrep', '-f', sprintf('messenger:consume %s', $transport)]);
        $check->run();

        if ($check->isSuccessful()) {
            if ( ! $restart) {
                $output->writeln(sprintf('Worker "%s" already enabled.', $transport));

                return;
            }

            $output->writeln(sprintf('Restarting worker "%s"...', $transport));

            $pids = explode("\n", trim($check->getOutput()));

            foreach ($pids as $pid) {
                if ($pid !== '') {
                    (new Process(['/bin/kill', '-KILL', $pid]))->run();
                }
            }
        } else {
            $output->writeln(sprintf('Worker "%s" disabled. Starting...', $transport));
        }

        $projectDir = $this->kernel->getProjectDir();

        $command = sprintf(
            'nohup /usr/bin/php %s/bin/console messenger:consume %s --sleep=1 > /dev/null 2>&1 &',
            escapeshellarg($projectDir),
            escapeshellarg($transport),
        );

        for ($i = 1; $i <= $numWorkers; $i++) {
            Process::fromShellCommandline($command)->run();
        }

        $output->writeln(sprintf('Worker "%s" started.', $transport));
    }
}
