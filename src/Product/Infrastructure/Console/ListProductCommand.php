<?php

declare(strict_types=1);

namespace App\Product\Infrastructure\Console;

use App\Product\Domain\ProductTransfer;
use App\Product\ProductFacade;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class ListProductCommand extends Command
{
    protected static $defaultName = 'gacela:product:list';

    protected function configure(): void
    {
        $this->setDescription('List all products');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $facade = new ProductFacade();
        $productTransfers = $facade->getAllProducts();

        foreach ($productTransfers as $productTransfer) {
            $output->writeln(sprintf(
                'Product name: %s, price: %s',
                $productTransfer->getName(),
                $productTransfer->getPrice(),
            ));
        }

        return Command::SUCCESS;
    }
}
