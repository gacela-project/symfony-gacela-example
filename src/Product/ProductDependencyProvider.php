<?php

declare(strict_types=1);

namespace App\Product;

use App\Product\Infrastructure\Persistence\ProductRepository;
use Gacela\Framework\AbstractDependencyProvider;
use Gacela\Framework\Container\Container;

final class ProductDependencyProvider extends AbstractDependencyProvider
{
    public const PRODUCT_REPOSITORY = 'PRODUCT_REPOSITORY';

    public function provideModuleDependencies(Container $container): void
    {
        $container->set(
            self::PRODUCT_REPOSITORY,
            fn() => $container->getLocator()->get(ProductRepository::class)
        );
    }
}
