<?php

declare(strict_types=1);

namespace App\Product\Infrastructure\Console;

use App\Product\Infrastructure\PriceInput;
use App\Product\ProductFacade;
use Gacela\Framework\ServiceResolverAwareTrait;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @method ProductFacade getFacade()
 */
#[AsCommand(name: 'gacela:product:add', description: 'Add new product')]
final class AddProductCommand extends Command
{
    use ServiceResolverAwareTrait;

    protected function configure(): void
    {
        $this->addArgument('name', InputArgument::REQUIRED)
            ->addArgument('price', InputArgument::OPTIONAL, '1');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $name = (string) $input->getArgument('name');
        $priceArgument = $input->getArgument('price');
        $price = is_string($priceArgument) ? $priceArgument : null;

        $this->getFacade()->createNewProduct($name, PriceInput::parse($price));

        $output->writeln($name . ' product created successfully');

        return Command::SUCCESS;
    }
}
