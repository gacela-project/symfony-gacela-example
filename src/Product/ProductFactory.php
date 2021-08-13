<?php

declare(strict_types=1);

namespace App\Product;

use App\Product\Application\ProductCreator;
use App\Product\Application\ProductLister;
use App\Product\Domain\ProductEntityManagerInterface;
use App\Product\Domain\ProductRepositoryInterface;
use Gacela\Framework\AbstractFactory;

/**
 * @method ProductConfig getConfig()
 */
final class ProductFactory extends AbstractFactory
{
    private ProductRepositoryInterface $productRepository;
    private ProductEntityManagerInterface $productEntityManager;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        ProductEntityManagerInterface $productEntityManager
    ) {
        $this->productRepository = $productRepository;
        $this->productEntityManager = $productEntityManager;
    }

    public function createProductCreator(): ProductCreator
    {
        return new ProductCreator(
            $this->productEntityManager,
            $this->getConfig()->getDefaultProductPrice()
        );
    }

    public function createProductLister(): ProductLister
    {
        return new ProductLister($this->productRepository);
    }
}
