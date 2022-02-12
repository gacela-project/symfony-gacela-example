<?php

declare(strict_types=1);

namespace App\Product;

use App\Product\Application\ProductCreator;
use App\Product\Infrastructure\Persistence\ProductEntityManager;
use App\Product\Infrastructure\Persistence\ProductRepository;
use Gacela\Framework\AbstractFactory;

/**
 * @method ProductConfig getConfig()
 * @method ProductRepository getRepository()
 * @method ProductEntityManager getEntityManager()
 */
final class ProductFactory extends AbstractFactory
{
    public function createProductCreator(): ProductCreator
    {
        return new ProductCreator(
            $this->getEntityManager(),
            $this->getConfig()->getDefaultProductPrice()
        );
    }
}
