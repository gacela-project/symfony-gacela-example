<?php

declare(strict_types=1);

namespace App\Calculator\Infrastructure\Console\Commands;

use App\Calculator\CalculatorFacade;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class AdderCommand extends Command
{
    protected static $defaultName = 'gacela:add';

    protected function configure(): void
    {
        $this->setDescription('Add multiple numbers')
            ->addArgument(
                'numbers',
                InputArgument::IS_ARRAY | InputArgument::OPTIONAL
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $numberArguments = $input->getArgument('numbers');
        $numbers = array_map(static fn (string $n) => (int)$n, $numberArguments);

        $output->writeln((new CalculatorFacade())->add(...$numbers));

        return Command::SUCCESS;
    }
}
