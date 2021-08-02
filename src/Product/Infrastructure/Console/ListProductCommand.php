<?php

declare(strict_types=1);

namespace App\Product\Infrastructure\Console;

use App\Product\ProductFacade;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class ListProductCommand extends Command
{
    protected static $defaultName = 'gacela:product:list';

    private ProductFacade $productFacade;

    public function __construct(ProductFacade $productFacade)
    {
        parent::__construct(self::$defaultName);
        $this->productFacade = $productFacade;
    }

    protected function configure(): void
    {
        $this->setDescription('List all products');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $products = $this->productFacade->getAllProducts();

        foreach ($products as $product) {
            $output->writeln(sprintf(
                'Product name: %s, price: %s',
                $product->getName(),
                $product->getPrice(),
            ));
        }

        return Command::SUCCESS;
    }
}
