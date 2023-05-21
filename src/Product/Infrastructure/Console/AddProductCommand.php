<?php

declare(strict_types=1);

namespace App\Product\Infrastructure\Console;

use App\Product\ProductFacade;
use Gacela\Framework\DocBlockResolverAwareTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @method ProductFacade getFacade()
 */
final class AddProductCommand extends Command
{
    use DocBlockResolverAwareTrait;

    protected static $defaultName = 'gacela:product:add';

    protected function configure(): void
    {
        $this->setDescription('Add new product')
            ->addArgument('name', InputArgument::REQUIRED)
            ->addArgument('price', InputArgument::OPTIONAL, '1');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $name = $input->getArgument('name');
        $price = $input->getArgument('price');

        $this->getFacade()->createNewProduct($name, $this->validatePriceInput($price));

        $output->writeln($name . ' product created successfully');

        return Command::SUCCESS;
    }

    private function validatePriceInput(?string $price): ?int
    {
        if ($price === null) {
            return null;
        }

        if (filter_var($price, FILTER_VALIDATE_INT) === 0 || !filter_var($price, FILTER_VALIDATE_INT) === false) {
            return (int)$price;
        }

        throw new \RuntimeException('Second parameter [price] must be of type integer');
    }
}
