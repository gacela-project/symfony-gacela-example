<?php

declare(strict_types=1);

namespace App\Product;

use App\Shared\Domain\Entity\Product;
use Gacela\Framework\AbstractFacade;

final class ProductFacade extends AbstractFacade
{
    private ProductFactory $productFactory;

    public function __construct(ProductFactory $productFactory)
    {
        $this->productFactory = $productFactory;
    }

    public function createNewProduct(string $name, ?int $price = null): void
    {
        $this->productFactory
            ->createProductCreator()
            ->createProduct($name, $price);
    }

    /**
     * @return list<Product>
     */
    public function getAllProducts(): array
    {
        return $this->productFactory
            ->createProductLister()
            ->getAllProducts();
    }
}
