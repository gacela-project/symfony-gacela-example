<?php

declare(strict_types=1);

namespace App\Product;

use App\Product\Domain\ProductRepositoryInterface;
use Gacela\Framework\AbstractProvider;
use Gacela\Framework\Container\Container;

final class ProductProvider extends AbstractProvider
{
    public const PRODUCT_REPOSITORY = 'PRODUCT_REPOSITORY';

    public function provideModuleDependencies(Container $container): void
    {
        $container->set(
            self::PRODUCT_REPOSITORY,
             fn() => $container->getLocator()->get(ProductRepositoryInterface::class)
        );
    }
}
