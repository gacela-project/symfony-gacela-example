<?php

declare(strict_types=1);

namespace App\Product\Application;

use App\Product\Domain\ProductEntityManagerInterface;
use App\Shared\Domain\Entity\Product;

final class ProductCreator
{
    private ProductEntityManagerInterface $productEntityManager;

    private int $defaultPrice;

    public function __construct(
        ProductEntityManagerInterface $productEntityManager,
        int $defaultPrice
    ) {
        $this->productEntityManager = $productEntityManager;
        $this->defaultPrice = $defaultPrice;
    }

    public function createProduct(string $name, ?int $price = null): void
    {
        $product = (new Product())
            ->setName($name)
            ->setPrice($price ?? $this->defaultPrice);

        $this->productEntityManager->save($product);
        # send events, emails, or whatever
    }
}
