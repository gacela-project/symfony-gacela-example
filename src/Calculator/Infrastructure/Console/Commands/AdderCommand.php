<?php

declare(strict_types=1);

namespace App\Calculator\Infrastructure\Console\Commands;

use App\Calculator\Facade;
use Gacela\Framework\Container\Locator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class AdderCommand extends Command
{
    //protected static $defaultName = 'gacela:add {number*}';
    protected static $defaultName = 'gacela:add';

    public function setDescription(string $description)
    {
        // ?????????
        return 'Add multiple numbers';
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
//        $numberArguments = $input->getArgument('number');
//        $numbers = array_map(static fn (string $n) => (int)$n, $numberArguments);
//
//        $output->writeln($this->getFacade()->add(...$numbers));


        $output->write('Hola');
        $output->write((new Facade())->add(2, 3));

        return Command::SUCCESS;
    }
}
