<?php

declare(strict_types=1);

namespace App\Product;

use App\Product\Application\ProductCreator;
use App\Product\Application\ProductLister;
use App\Shared\Infrastructure\Repository\ProductRepository;
use Gacela\Framework\AbstractFactory;

/**
 * @method ProductConfig getConfig()
 */
final class ProductFactory extends AbstractFactory
{
    private ProductRepository $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function createProductCreator(): ProductCreator
    {
        return new ProductCreator(
            $this->productRepository,
            $this->getConfig()->getDefaultProductPrice()
        );
    }

    public function createProductLister(): ProductLister
    {
        return new ProductLister($this->productRepository);
    }
}
