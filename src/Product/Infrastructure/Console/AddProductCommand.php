<?php

declare(strict_types=1);

namespace App\Product\Infrastructure\Console;

use App\Product\ProductFacade;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class AddProductCommand extends Command
{
    protected static $defaultName = 'gacela:product:add';

    private ProductFacade $productFacade;

    public function __construct(ProductFacade $productFacade)
    {
        parent::__construct(self::$defaultName);
        $this->productFacade = $productFacade;
    }

    protected function configure(): void
    {
        $this->setDescription('Add new product')
            ->addArgument('name', InputArgument::REQUIRED)
            ->addArgument('price', InputArgument::OPTIONAL);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $name = $input->getArgument('name');
        $price = (int)$input->getArgument('price');

        $this->validatePriceInput($price);

        $this->productFacade->createNewProduct($name, $price);

        $output->writeln($name . ' product created successfully');

        return Command::SUCCESS;
    }

    private function validatePriceInput($price): void
    {
        if (null !== $price && !filter_var($price, FILTER_VALIDATE_INT)) {
            throw new \RuntimeException('Second parameter [price] must be of type integer');
        }
    }
}
