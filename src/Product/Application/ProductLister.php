<?php

declare(strict_types=1);

namespace App\Product\Application;

use App\Product\Domain\Repository\ProductRepositoryInterface;
use App\Shared\Domain\Entity\Product;

final class ProductLister
{
    private ProductRepositoryInterface $productRepository;

    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * @return list<Product>
     */
    public function getAllProducts(): array
    {
        return $this->productRepository->findAll();
    }
}
