<?php

declare(strict_types=1);

namespace App\Command;

use App\Calculator\Facade;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestCommand extends Command
{
    protected static $defaultName = 'app:calculator';

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Hi');
        $output->writeln((new Facade())->add(2, 3));

        return Command::SUCCESS;
    }
}
