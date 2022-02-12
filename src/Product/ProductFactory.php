<?php

declare(strict_types=1);

namespace App\Product;

use App\Product\Application\ProductCreator;
use App\Product\Application\ProductLister;
use App\Product\Infrastructure\Persistence\ProductRepository;
use Gacela\Framework\AbstractFactory;

/**
 * @method ProductConfig getConfig()
 * @method ProductRepository getRepository()
 */
final class ProductFactory extends AbstractFactory
{
    public function createProductCreator(): ProductCreator
    {
        return new ProductCreator(
            $this->getRepository(),
            $this->getConfig()->getDefaultProductPrice()
        );
    }

    public function createProductLister(): ProductLister
    {
        return new ProductLister($this->getRepository());
    }
}
