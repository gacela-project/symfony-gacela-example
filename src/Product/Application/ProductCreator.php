<?php

declare(strict_types=1);

namespace App\Product\Application;

use App\Product\Domain\ProductRepositoryInterface;
use App\Product\Domain\ProductTransfer;

final class ProductCreator
{
    private ProductRepositoryInterface $repository;

    private int $defaultPrice;

    public function __construct(
        ProductRepositoryInterface $productEntityManager,
        int $defaultPrice
    ) {
        $this->repository = $productEntityManager;
        $this->defaultPrice = $defaultPrice;
    }

    public function createProduct(string $name, ?int $price = null): void
    {
        $product = (new ProductTransfer())
            ->setName($name)
            ->setPrice($price ?? $this->defaultPrice);

        $this->repository->save($product);
        # send events, emails, or whatever
    }
}
