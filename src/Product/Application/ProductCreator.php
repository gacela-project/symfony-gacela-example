<?php

declare(strict_types=1);

namespace App\Product\Application;

use App\Product\Domain\Repository\ProductRepositoryInterface;
use App\Shared\Domain\Entity\Product;

final class ProductCreator
{
    private ProductRepositoryInterface $productRepository;

    private int $defaultPrice;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        int $defaultPrice
    ) {
        $this->productRepository = $productRepository;
        $this->defaultPrice = $defaultPrice;
    }

    public function createProduct(string $name, ?int $price = null): void
    {
        $product = (new Product())
            ->setName($name)
            ->setPrice($price ?? $this->defaultPrice);

        $this->productRepository->save($product);
        # send events, or emails, or whatever
    }
}
