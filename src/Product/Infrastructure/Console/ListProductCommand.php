<?php

declare(strict_types=1);

namespace App\Product\Infrastructure\Console;

use App\Product\Domain\ProductTransfer;
use App\Product\ProductFacade;
use Gacela\Framework\DocBlockResolverAwareTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @method ProductFacade getFacade()
 */
final class ListProductCommand extends Command
{
    use DocBlockResolverAwareTrait;

    protected static $defaultName = 'gacela:product:list';

    protected static $defaultDescription = 'List all products';

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $productTransfers = $this->getFacade()->getAllProducts();

        foreach ($productTransfers as $productTransfer) {
            $output->writeln($this->productAsString($productTransfer));
        }

        return Command::SUCCESS;
    }

    private function productAsString(ProductTransfer $productTransfer): string
    {
        return sprintf(
            'Product name: %s, price: %s',
            $productTransfer->getName(),
            $productTransfer->getPrice(),
        );
    }
}
